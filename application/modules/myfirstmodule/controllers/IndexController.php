<?php
/**
 * Index controller
 * 
 * @category application
 * @package myfirstmodule
 * @copyright Mimesis Republic
 */

/**
 * Myfirstmodule_IndexController
 * 
 * @category application
 * @package myfirstmodule
 */
class Myfirstmodule_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->access->permissionDisabled = array('all');
    }

    public function indexAction()
    {
      echo "myfirstmodule";
      exit;
    }
}

