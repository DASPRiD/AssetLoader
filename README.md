AssetLoader module for ZF2
==========================

Introduction
------------
AssetLoader is a module for ZF2 to ease the loading of assets within development
from multiple modules. This is not meant for production, as it adds quite a bit
of overhead. When deploying this module (will) supply a script to compile all
assets into the public directory of the application.

Usage
-----
To use the module, it is important to add it to your application config as the
very first module, as it relies on the loadModule event. After that you must add
a method called "getAssetPath()" to all your modules "Module.php" files which
contain public assets, and return an absolute path to the assets there. For
instance, when your assets are located in a directory called "public":

    public function getAssetPath()
    {
        return __DIR__ . '/public';
    }