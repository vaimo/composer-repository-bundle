# composer-repository-bundle

Allows composer package installation from repositories or zip files that have multiple packages 
inside of them. 

## Configuration: hard-points

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
                "url": "https://github.com/magento-research/pwa-studio/archive/master.tar.gz",
                "paths": ["packages"]
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
                "paths": ["packages"],
                "reference": "9c6dfcc955df4b88218cd6c0eb6d0260df27117d"
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

After bundles have been registered in composer.json, user can just install them as any other composer
package. 

Note that the deployed package will be mirrored to vendor folder, rather than copied. This is due to the 
fact that the bundle itself is kept within composer cache rather than in the project root.   

## Changelog 

_Changelog included in the composer.json of the package_
