<?php
namespace GbiliFilesAsStorageModule;

class Module 
{
    public function getConfig()
    {
        return include __DIR__ . '/../../../module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
