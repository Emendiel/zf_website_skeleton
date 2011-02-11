<?php
/**
 * Index controller
 * 
 * @category application
 * @package default
 * @copyright Mimesis Republic
 */

/**
 * IndexController
 * 
 * @category application
 * @package default
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function indexAction()
    {
    }
    
    public function registerAction()
    {

    }
    
    public function phpinfoAction()
    {
        phpinfo();
        exit;
    }

    public function forbiddenAction()
    {
        echo "forbidden";
        exit;
    }
}

