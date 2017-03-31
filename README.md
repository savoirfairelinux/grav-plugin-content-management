# Content Management Plugin

The **Content Management** Plugin is for [Grav CMS](http://github.com/getgrav/grav). Savoir-faire Linux' content management plugin helps you create a content approbation workflow for your [Grav][grav] website using the power of [git][git].

This plugins assumes that it can create commits from the `pages` folder, and that it can push to `origin` using the webserver user, and no other credentials.

If you also have a live website on the same server, you can specify two other variables so that the plugin can push the content to that instance.

## Installation

Installing the Content Management plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install content-management

This will install the Content Management plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/content-management`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `content-management`. You can find these files on [GitHub](https://github.com/savoirfairelinux/grav-plugin-content-management) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/content-management

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Admin][admin] plugin to operate.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/content-management/content-management.yaml` to `user/config/plugins/content-management.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
live_repo: ''
live_cache_invalidator: ''
```

## Usage

You need the [admin][admin] plugin to use this plugin. You will also have to give to at least one user the `contentManagement` right in the `user.access.admin` section.

This user will then be able to access the *Content manager* page in the left menu of the admin. This page will let her send content to the *live* website, if defined. If not, that commit will be tagged and pushed to `origin`, where you could run a post-receive hook to publish that content.

## Credits

Huge thanks to [Savoir-faire Linux][savoirfairelinux], my employer at the time of creation of this plugin, to have let me play around this technology.

## To Do

- [ ] Beautify `detail` page.

[savoirfairelinux]: https://savoirfairelinux.com
[admin]: https://github.com/getgrav/grav-plugin-admin
[grav]: http://github.com/getgrav/grav
[git]: https://git-scm.com/
