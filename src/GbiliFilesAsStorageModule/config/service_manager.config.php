<?php
namespace GbiliFilesAsStorageModule;

return array(
    'factories' => array(
        'GbiliFilesAsStorageModule\Service\FilesAsStorage' => function ($sm) {
            $config  = $sm->get('Config');
            $dir     = $config['array_storage_dir'];
            $service = new Service\FilesAsStorage($dir);
            return $service;
        },
    ),
    'aliases' => array(
        'gbiliFilesAsStorage' => 'GbiliFilesAsStorageModule\Service\FilesAsStorage',
    ),
);

