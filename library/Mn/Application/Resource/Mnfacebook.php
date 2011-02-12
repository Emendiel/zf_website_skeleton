<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Facebook.php 21015 2010-02-11 01:56:02Z freak $
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for setting up Facebook
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Application_Resource_Mnfacebook extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY = 'Mn_Facebook';

    /**
     * @var Facebook
     */
    protected $_facebook;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Facebook
     */
    public function init()
    {
        return $this->getMnFacebook();
    }

    public function getMnFacebook()
    {
        if (null === $this->_facebook)
        {
            $options = $this->getOptions();
            $this->_facebook = Mn_Facebook::factory($options);

            //set in registry
            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
            ? $options['registry_key']
            : self::DEFAULT_REGISTRY_KEY;
            Zend_Registry::set($key, $this->_facebook);
        }

        return $this->_facebook;
    }
}
