# composer-repository-bundle

Allows composer package installation from repositories or zip files that have multiple packages 
inside of them. 

## Configuration: overview

Environment variables can be defined as key value pairs in the project's composer.json

```json
{
  "extra": {
    "bundles": {},
    "bundles-package": {}
  }
}
```

These values will be declared for system-wide use. The main idea of the module is to provide
a way to pre-configure any flags for any of the composer plugins in case the flag setting
has not been properly exposed to the end-user.
  
## Configuring bundle

Can be done against zip file ...

```json
{
    "extra": {
        "bundles": {
            "magento-research/pwa-studio": {
                "url": "https://github.com/magento-research/pwa-studio/archive/master.tar.gz"
            }
        }
    }
}
```

Same can be done against repository (in which case either branch name of change-set reference is required)

```json
{
    "extra": {
        "bundles": {
            "magento-research/pwa-studio": {
                "url": "git@github.com:magento-research/pwa-studio.git",
                "reference": "9c6dfcc955df4b88218cd6c0eb6d0260df27117d"
            }
        }
    }
}
```

The plugin can also be used to configure local project-embedded bundle folders from where modules will
become installable.

```json
{
    "extra": {
        "bundles": {
            "my/project_name": {
                "source": "modules"
            }
        }
    }
}
```

Which would allow any module to be installed from <project-root>/modules - note that the modules from a local 
bundle like this will sym-linked instead of being mirrored. Not configuring "source" at all will cause 
the project root to be considered as a folder for installation (can be combined with sub-folder config).

## Configuring bundle sub-folders

By default, the bundle repository will consider every sub-folder on the main level of the bundle as potential
installable package, in case the packages are available in some sub-folder(s), relative paths can be defined.

```json
{
    "extra": {
        "bundles": {
            "magento-research/pwa-studio": {
                "url": "https://github.com/magento-research/pwa-studio/archive/master.tar.gz",
                "paths": ["packages"]
            }
        }
    }
}
```

## Configuring package template

In case some of the installable sub-folders of the bundle are not directly installable (lack composer.json), 
the bundle plugin will create the missing package definitions. The only requirement is that the package.

If there are special parts of the composer.json that need to be defined, declare those under 'extra-package'
as in same format as one would be declaring normal package configuration. The contents will be used as default
values for generated package definitions:

```json
{
    "extra": {
        "bundles-package": {
            "autoload": {
                "files": ["registration.php"]
            }
        }
    }
}
```

## Configuring custom target path for bundle download

In case you want bundle to be downloaded into the root of your directory, configure a target folder for it.

```json
{
    "extra": {
        "bundles": {
            "magento-research/pwa-studio": {
                "url": "https://github.com/magento-research/pwa-studio/archive/master.tar.gz",
                "paths": ["packages"],
                "target": "pwa-studio"
            }
        }
    }
}
```

Note that the package installation in this case will result in packages being sym-linked instead of being
mirrored. 

## Installing packages from bundle

Make sure that you have installed this plugin separately before you start installing packages from
registered bundles.

After bundles have been registered in composer.json, user can just install them as any other composer
package. Note that package versions are ignored, use dev-bundle instead. 

    composer require magento/theme-frontend-venia:'dev-magento-research/pwa-studio'

Note that 'composer require' is somewhat special as a command and does require a non-version string
to be used when adding the module to the repository. The constraint will be generated from bundle
repository name, so in case you want to require the package as "dev-local", use the following:

```json
{
    "extra": {
        "bundles": {
            "local": "modules"
        }
    }
}
```

Note that this configuration will use the folder <project-root>/modules as bundle repository and packages
from there can be installed with

    composer require vaimo/some-package:'dev-local' 

### Alternative installation (installing with version)     

In case you added the module to the project's composer.json manually, any version string can be used. So
you could, for example, declare  magento/theme-frontend-venia requirement in composer.json as "1.0.0". In
this case, the installation would be:
 
    # Step 1: add the "magento/theme-frontend-venia": "1.0.0" to composer.json manually
    
    # Step 2: run composer update to install the module 
    composer update magento/theme-frontend-venia


## Bundle package deployment

There are two ways that the package might end up being deployed to the project's vendor:

* sym-linked - done when bundle situated under the project root (bundle is part of the project).
* mirrored - done when bundle situates in composer package cache (bundle is part of global composer). 

## Changelog 

_Changelog included in the composer.json of the package_
