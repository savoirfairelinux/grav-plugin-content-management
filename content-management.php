<?php
namespace Grav\Plugin;

require __DIR__ . '/vendor/autoload.php';

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;
use PHPGit\Git;

/**
 * Class ContentManagementPlugin
 * @package Grav\Plugin
 */
class ContentManagementPlugin extends Plugin
{
    protected $route = 'content-management';

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onTask.toProd'   => ['onTaskToProd', 0],
            'onTask.newCommit'   => ['onTaskNewCommit', 0],
            'onAdminAfterSave' => ['onAdminAfterSave', 0],
            'onAdminAfterDelete' => ['onAdminAfterSave', 0],
            'onAdminAfterAddMedia' => ['onAdminAfterSave', 0],
            'onAdminAfterDelMedia' => ['onAdminAfterSave', 0]
        ];
    }

    /**
     * automatically create a commit after a save
     */
    public function onAdminAfterSave()
    {
        $this->createNewCommit();
    }

    /**
     * send content to Prod
     */
    public function onTaskToProd()
    {
        $post = !empty($_POST) ? $_POST : [];

        if ($this->grav['user']['access']['admin']['contentManagement']) {

            if (isset($post['hash'])) {
                $this->toProd($post['hash']);
                $this->grav->redirect('admin/content-management/toProd');
            }
            $this->grav->redirect('admin/content-management/errorNoHashProd');
        }

        $this->grav->redirect('admin/content-management/errorRightsProd');
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if (!$this->isAdmin()) {
            return;
        }

        // Autoload classes
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new \Exception('Admin Plugin failed to load. Composer dependencies not met.');
        }
        require_once $autoload;

        // Check for required plugins
        if (!$this->grav['config']->get('plugins.admin.enabled')) {
            throw new \RuntimeException('One of the required plugins is missing or not enabled');
        }

        // Enable the main event we are interested in
        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onAdminMenu' => ['onAdminMenu', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 1000],
        ]);

        /** @var Uri $uri */
        $uri = $this->grav['uri'];

        // If we're not in contentManagement plugin, exit
        if (strpos($uri->path(), $this->config->get('plugins.admin.route') . '/' . $this->route) === false) {
            return;
        }

        // If we are displaying detail of a commit
        if (strpos($uri->path(), 'detail') !== false) {
            $this->displayDetails($uri);
        }

        elseif (!isset($uri->paths()[2])) {
            $this->displayGeneralView();
        }
    }

    private function displayGeneralView() {
            $repo = new Git();
            $repo->setRepository($this->grav['locator']->findResource('user://pages', true));
            $logs = $repo->log();
            $this->grav['twig']->logs = $logs;

            $allTags = $repo->tag();
            $prod = array_pop($allTags);
            $prodLog = $repo->log($prod, null, array('limit' => 1));
            $this->grav['twig']->prod = $prodLog[0];

    }

    private function displayDetails($uri) {
        $repo = new Git();
        $repo->setRepository($this->grav['locator']->findResource('user://pages', true));
        $hash = $uri->paths()[3];

        $detail = $repo->show($hash);
        if (!$detail) {
            $this->grav->redirect('admin/content-management/errorRightsProd');

        }
        $allTags = $repo->tag();
        $prod = array_pop($allTags);
        $diff = $repo->diff($prod, $hash);

        $prodLog = $repo->log($prod, null, array('limit' => 1));
        $this->grav['twig']->prod = $prodLog[0];

        $this->grav['twig']->hash = $hash;
        $this->grav['twig']->detail = $detail;

        $this->grav['twig']->diff = $diff;
    }

    /**
     * Add plugin templates path
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/admin/templates';
    }

    /**
     * Add navigation item to the admin plugin
     */
    public function onAdminMenu()
    {
        $this->grav['twig']->plugins_hooked_nav['PLUGIN_CONTENT_MANAGEMENT.CONTENT_MANAGEMENT'] = ['route' => $this->route, 'icon' => 'fa-database'];
    }

    private function createNewCommit()
    {
        $repo = new Git();
        $repo->setRepository($this->grav['locator']->findResource('user://pages', true));

        $status = $repo->status();
        if (count($status['changes'])) {
            $repo->add('.');
            $repo->commit('new changes made by '.$this->grav['user']->fullname, array('all'=>true));
            $repo->push('origin', 'master');
        }
    }

    private function toProd($hash)
    {
        $localRepo = new Git();
        $localRepo->setRepository($this->grav['locator']->findResource('user://pages', true));
        $localRepo->tag->create('prod'.time(), $hash, array('annotate'=>true, 'message'=>'This was pushed to prod by '.$this->grav['user']->fullname));
        $localRepo->push('origin', 'master', array('tags'=> true));

        $liveRepo = $this->config->get('plugins.content-management.live_repo');
        if ($liveRepo) {
            $liveRepo = new Git();
            $liveRepo->setRepository($liveRepo);
            $liveRepo->fetch('origin');
            $liveRepo->reset->hard($hash);

            $liveCacheInvalidator = $this->config->get('plugins.content-management.live_cache_invalidator');
            if ($liveCacheInvalidator) {
                exec($liveCacheInvalidator.' clear-cache --all');
            }
        }
        $this->grav['twig']->success = true;
    }

    /**
     * Set all twig variables for generating output.
     */
    public function onTwigSiteVariables()
    {
        $twig = $this->grav['twig'];
    }
}
