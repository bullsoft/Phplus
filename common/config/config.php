<?php
// Visiable Variables
// $root           -- dir of the app
// $phplusDir      -- dir of the Phplus library
// $di             -- global di container
// $config         -- the Phalcon\Config\Config object
// $app            -- app object
// $loader         -- Phalcon\Loader object

return [
    "application" => array(
        "debug" => false,
        "close" => false,
    ),
    'namespace' => array(
        "App\\Com\\Protos"         => $phplusDir . '/common/protos/',
        "PhalconPlus\\Com\\Protos" => $phplusDir . '/common/protos/',
    ),
];


/* config.php ends here */
