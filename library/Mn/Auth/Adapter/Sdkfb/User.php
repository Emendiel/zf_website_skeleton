<?php

class Mn_Auth_Adapter_Sdkfb_User
{
    /**
    * Data of the user
    *
    * @var object
    */
    protected $_data = null;
    
    public function __construct($p_oData = null)
    {
        $this->_data = $p_oData;
    }
    
    public function getData(){
        return $this->_data;
    }
}
