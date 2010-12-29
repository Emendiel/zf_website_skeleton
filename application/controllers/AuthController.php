<?php

class AuthController extends Zend_Controller_Action
{
    protected $aclDisabled = array('all');

    public function init()
    {
        $this->_helper->access->permissionDisabled = array('all');
    }
}

