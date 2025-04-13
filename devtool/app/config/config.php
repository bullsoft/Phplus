<?php
// Visiable Variables
// $root           -- dir of the app
// $di             -- global di container
// $config         -- the Phalcon\Config\Config object
// $app            -- app object
// $loader         -- Phalcon\Loader object

return [
    'application' => [
        "name"  => "fp-devtool",
        "ns"    => "PhalconPlus\\DevTools\\",
        "mode"  => "Cli",
    ],
    "logger" => [
        [
            "filePath" => "/tmp/fp-devtool.log.debug",
            "level" => \Phalcon\Logger\Logger::DEBUG
        ],
        [
            "filePath" => "/tmp/fp-devtool.log",
            "level" => \Phalcon\Logger\Logger::CUSTOM
        ]
    ],
    'version' => "1.2.1",
];

/* config.php ends here */
