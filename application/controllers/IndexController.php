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
        var_dump(Zend_Registry::get('Mn_Thrift')->getThrift('test')->listRooms());exit;
    }
}
