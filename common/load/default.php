<?php
// Visiable Variables
// $root           -- dir of the app
// $phplusDir      -- dir of the Phplus library
// $di             -- global di container
// $config         -- the Phalcon\Config\Config object
// $app            -- app object
// $loader         -- Phalcon\Loader object

mb_internal_encoding("UTF-8");

// register global class-dirs, class-namespace and class-prefix
$globalNs = $config->namespace->toArray();
$loader->setNamespaces($globalNs, true)
       ->register();


// global funciton to retrive $di
if (!function_exists("getDI")) {
    function getDI()
    {
        global $app;
        return $app->di();
    }
}

if (!function_exists("di")) {
    function di()
    {
        global $app;
        return $app->di();
    }
}

if (!function_exists("supername")) {
    function supername(string $ns, int $levels)
    {
        $dir = strtr($ns, "\\", "/");
        $here = dirname($dir, $levels);
        return strtr($here, "/", "\\");
    }
}

/* default.php ends here */
