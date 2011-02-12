<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Mnsdk.php.php 21015 2010-02-11 01:56:02Z freak $
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for setting up sdk
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Application_Resource_Mnsdk extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY = 'Mn_Sdk';

    /**
     * @var Sdk
     */
    protected $_sdk;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Sdk
     */
    public function init()
    {
        return $this->getMnSdk();
    }

    public function getMnSdk()
    {
        if (null === $this->_sdk)
        {
            $options = $this->getOptions();
            $this->_sdk = new Mn_Sdk($options);

            //set in registry
            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
            ? $options['registry_key']
            : self::DEFAULT_REGISTRY_KEY;
            Zend_Registry::set($key, $this->_sdk);
        }

        return $this->_sdk;
    }
}
