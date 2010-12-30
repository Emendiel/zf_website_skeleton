<?php

/**
 * This file is part of Turbine
 * http://github.com/SirPepe/Turbine
 * 
 * Copyright Peter Kröner
 * Licensed under GNU LGPL 3, see license.txt or http://www.gnu.org/licenses/
 */


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

include('Zend/Config/Yaml.php');

$configFile = new Zend_Config_Yaml(APPLICATION_PATH . '/configs/application.yaml', APPLICATION_ENV);


$config = array(


// Sets debugging off (0), on (1), or in developer mode (2)
// Mode 0 hides all error messages
// Mode 1 displays error messages related to the style sheets (like elements trying to inherit properties that don't exist)
// Mode 2 additionally displays php developer messages and sets error_reporting to E_ALL
'debug_level' => (isset($configFile->turbine) && isset($configFile->turbine->debug_level)) ? $configFile->turbine->debug_level : 0,


// Base path to cssp and css files relative to css.php
'css_base_dir' => (isset($configFile->turbine) && isset($configFile->turbine->css_base_dir)) ? $configFile->turbine->css_base_dir : '../stylesheets/',


// Minify regular css files (true) oder include them completely unchanged (false)
'minify_css' => (isset($configFile->turbine) && isset($configFile->turbine->minify_cssl)) ? $configFile->turbine->minify_css : true


);


?>