<?php
namespace AssetLoader;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager;

/**
 * Module for loading assets in development.
 */
class Module
{
    /**
     * Collected asset paths.
     *
     * @var array
     */
    protected $assetPaths = array();

    /**
     * Initialize the module.
     *
     * @param  Manager $moduleManager
     * @return void
     */
    public function init(Manager $moduleManager)
    {
        $moduleManager->events()->attach('loadModule', array($this, 'addAssetPath'));

        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Mvc\Application', 'route', array($this, 'checkRequestUriForAsset'), PHP_INT_MAX);
    }

    /**
     * Add an asset path from a module.
     *
     * @param  Zend\EventManager\Event $event
     * @return void
     */
    public function addAssetPath($event)
    {
        $module = $event->getModule();

        if (!method_exists($module, 'getAssetPath')) {
            return;
        }

        if (null !== ($assetPath = $module->getAssetPath())) {
            $this->assetPaths[] = rtrim($assetPath, '\\/');
        }
    }

    /**
     * Check a request for a valid file asset.
     *
     * @param  Zend\EventManager\Event $event
     * @return void
     */
    public function checkRequestUriForAsset($event)
    {
        $request = $event->getRequest();

        if (!method_exists($request, 'uri')) {
            return;
        }

        if (method_exists($request, 'getBaseUrl')) {
            $baseUrlLength = strlen($request->getBaseUrl() ?: '');
        } else {
            $baseUrlLength = 0;
        }

        $path = substr($request->uri()->getPath(), $baseUrlLength);

        foreach ($this->assetPaths as $assetPath) {
            if (file_exists($assetPath . $path)) {
                $this->sendFile($assetPath . $path);
            }
        }
    }

    /**
     * Send an asset file.
     *
     * @param  string $file
     * @return void
     */
    protected function sendFile($filename)
    {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($finfo, $filename);
        finfo_close($finfo);

        header('Content-Type: ' . $mimeType);
        readfile($filename);
        exit;
    }
}
