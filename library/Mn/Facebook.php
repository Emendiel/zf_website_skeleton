<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Facebook
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Facebook.php 21015 2010-02-11 01:56:02Z freak $
 */

/**
 * Facebook
 *
 * @category   Mn
 * @package    Mn_facebook
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Facebook
{
    /**
     * @var Facebook
     */
    static protected $_facebook;

    /**
     * Factory to construct Facebook API Client
     * based on the configuration array
     *
     * @param  array|Zend_Config Array or instance of Zend_Config
     * @return Zend_Log
     */
    static public function factory($config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config) || empty($config)) {
            /** @see Zend_Log_Exception */
            require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
        }
        
        self::$_facebook = new Mn_Facebook_Extended($config);
        return self::$_facebook;
    }
}
