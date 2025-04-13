<?php
// default-web.php 
// Visiable Variables
// $root           -- dir of the app
// $phplusDir      -- dir of the Phplus library
// $di             -- global di container
// $config         -- the Phalcon\Config\Config object
// $app            -- app object
// $loader         -- Phalcon\Loader object

require $phplusDir."/common/load/default.php";

// Pseudo-static Url
if(isset($_GET['_url'])) {
    $_GET['_url'] = str_replace(
        ['.html', '.htm', '.jsp', '.shtml', '.php'], 
        '', 
        $_GET['_url']
    );
}


/* default-web.php ends here */
