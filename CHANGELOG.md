# Changelog

_This file has been auto-generated from the contents of changelog.json_

## 1.7.2

### Fix

* implement the remaining methods in Vaimo\ComposerRepositoryBundle\Plugin

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.7.2) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.7.1...1.7.2)

## 1.7.1 (2022-05-04)

require vaimo/composer-changelogs: ^1.0
Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.7.1) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.7.0...1.7.1)

## 1.7.0 (2022-04-13)

composer 2.3 support
Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.7.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.6.0...1.7.0)

## 1.6.0 (2019-02-20)

### Feature

* allow glob patterns in repository path definitions to support deeper/grouped repository setups

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.6.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.5.0...1.6.0)

## 1.5.0 (2019-02-11)

### Feature

* allow forcing a bundle source to be installed with mirroring (instead of local bundles always ending up as sym-linked entities)

### Fix

* do not keep re-installing sym-linked modules on file changes (skip the md5 calculation for file contents)

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.5.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.4.3...1.5.0)

## 1.4.3 (2018-09-02)

### Fix

* make the module compatible with 5.3

### Maintenance

* introduced change-logs plugin dev dependency

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.4.3) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.4.2...1.4.3)

## 1.4.2 (2018-09-02)

### Fix

* allow the plugin to be uninstalled without a crash
* absolute paths in composer.lock cause issues on clean install

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.4.2) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.4.1...1.4.2)

## 1.4.1 (2018-08-09)

### Fix

* prevent a crash when accessing composer from non-cli environments due to accessing argv on the moment of plugin activation

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.4.1) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.4.0...1.4.1)

## 1.4.0 (2018-08-09)

### Feature

* allow shorter declaration of local project-specific repositories

### Fix

* require command not triggering bundle bootstrap (therefore 'composer require' was not usable to get bundle packages installed)
* don't fail when local bundle folder (configured in composer.json extra) does not exist
* use getcwd() for detecting project root directory instead of resolving it from composer config path which might be different when running global composer
* changed the default dev-* branch name that bundle modules are perceived with: dev-<bundle-name>
* making sure that calling bundle repo bootstrap twice does not required repositories multiple times

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.4.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.3.1...1.4.0)

## 1.3.1 (2018-07-23)

### Maintenance

* minor updated to packages's meta-data

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.3.1) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.3.0...1.3.1)

## 1.3.0 (2018-07-23)

### Feature

* allow package installation on any version when dealing with local package, rather than forcing user to use dev-default

### Fix

* updated bundle:info command output; name had a wrong value (origin path) under certain circumstances (local bundle)
* bundle list feature crash: one dependency missing a constructor argument
* bundle deploy not recognizing sym-linked packages

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.3.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.2.0...1.3.0)

## 1.2.0 (2018-07-18)

### Feature

* allow local folders to be defined as bundles (without the need to download anything)
* improved bundle configuration validation

### Fix

* do not modify/re-save composer.json of bundled packages if there are no changes to be made to them
* do not configure PSR-4 if there's already configuration in place

### Maintenance

* log output modified towards being less noisy when nothing is changed

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.2.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.1.0...1.2.0)

## 1.1.0 (2018-07-12)

### Feature

* new command added to list bundles and the packages they provide
* hide bundle package-list details on non-verbose run

### Maintenance

* unused code/classes removed

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.1.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/1.0.0...1.1.0)

## 1.0.0 (2018-07-12)

### Feature

* allow packages to be installed from sub-folders of repositories
* allow packages to be installed from sub-folders of zip files
* allow 'update' to pull in changes from the bundle when bundle files have changed
* introduced new command group: 'bundle', to allow fetching information about the bundle packages

Links: [src](https://github.com/vaimo/composer-repository-bundle/tree/1.0.0) [diff](https://github.com/vaimo/composer-repository-bundle/compare/2a73a640fa65fec178c9fbad7ffd2e9d06d3bd51...1.0.0)