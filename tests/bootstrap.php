<?php
/**
 * Bootstrap
 * 
 * @category
 * @version
 * @copyright
 */

/*
 * Start output buffering
 */
ob_start();

/*
 * Set error reporting to the level to which code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Set default timezone
 */
date_default_timezone_set('GMT');

/*
 * Testing environment
 */
define('APPLICATION_ENV', 'testing');
define('BASE_PATH', realpath(dirname(__FILE__) . '/../public'));
define('APPLICATION_PATH', realpath(BASE_PATH . '/../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/**
 * Register autoloader
 */
require_once "Zend/Loader/Autoloader.php";

$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $library, $models, $controllers, $tests, $path);

