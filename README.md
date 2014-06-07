GbiliFilesAsStorageModule
==============

Zf2 module. Provides Gbili/Db/Req integration.

Installation
============

Require the module:

Using composer, merge these lines into your project's composer.json file:

    {
        "repositories": [
            {
                "type":"vcs",
                "url":"https://github.com/gbili/GbiliFilesAsStorageModule"
            }
        ],
        "require": {
            "gbili/gbili-req-module": "dev-master"
        },
        "autoload": {
            "psr-0": {
            }
        }
    }

Currently we are reusing Doctrine's connection, but you can pass any valid PDO connection to Gbili\Db\Req
You can do that similarly than in GbiliFilesAsStorageModule/src/GbiliFilesAsStorageModule/Module.php:injectDbAdapter()
