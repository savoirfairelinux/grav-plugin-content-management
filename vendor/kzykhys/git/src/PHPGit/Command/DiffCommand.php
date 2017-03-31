<?php

namespace PHPGit\Command;

use PHPGit\Command;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Show the diff between
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class DiffCommand extends Command
{

    /**
     * Returns the diff between 2 commitish or 1 commitish and HEAD
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * echo $git->diff('HEAD~2');
     * ```
     *
     * ##### Output Example
     *
     * ```
     * ???
     * ```
     *
     * ##### Options
     *
     * @param string $committishFrom Committish object names to compute diff from.
     * @param string $committishTo [optional] Committish object names to compute diff to.
     * @param array  $options    [optional] An array of options {@see DiffCommand::setDefaultOptions}
     *
     * @return string
     */
    public function __invoke($committishFrom, $committishTo = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('diff');

        $this->addFlags($builder, $options, array());

        if ($committishFrom) {
            $builder->add($committishFrom);
        }
        if ($committishTo) {
            $builder->add($committishTo);
        }

        return trim($this->git->run($builder->getProcess()));
    }

    /**
     * Equivalent to $git->describe($committish, ['tags' => true]);
     *
     * @param string $committish [optional] Committish object names to describe.
     * @param array  $options    [optional] An array of options {@see DescribeCommand::setDefaultOptions}
     *
     * @return string
     */
    public function merge($committishFrom, $committishTo = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('diff');

        $this->addFlags($builder, $options, array());

        $commits = '';

        if ($committishFrom) {
            $commits = $commits.$committishFrom;
        }
        $commits = $commits.'...';
        if ($committishTo) {
            $commits = $commits.$committishTo;
        }
        $builder->add($commits);

        //die(var_dump($builder->getProcess()));

        return trim($this->git->run($builder->getProcess()));
    }

    /**
     * {@inheritdoc}
     *
     * - **patch**    (_boolean_) Generate patch (see section on generating patches).
     * - **no-patch**    (_boolean_) Suppress diff output. Useful for commands like git show that show the patch by default, or to cancel the effect of --patch.
     * - **raw**   (_boolean_) Enables matching a lightweight (non-annotated) tag
     * - **stat**    (_boolean_) Generate patch (see section on generating patches).
     * - **summary** (_boolean_) Show uniquely abbreviated commit object as fallback
     * - **no-prefix**    (_boolean_) Generate patch (see section on generating patches).
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'patch'    => true,
            'no-patch' => false,
            'raw'   => true,
            'stat'   => true,
            'summary' => true,
            'no-prefix' => true,
        ));
    }

}
