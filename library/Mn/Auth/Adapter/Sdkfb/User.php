<?php

class Mn_Auth_Adapter_Sdkfb_User
{
    /**
    * Data of the user
    *
    * @var array
    */
    protected $_data = null;

    /**
     * Retrieve data of the user
     *
     * null if no data exist
     *
     * @return mixed data
     */
    public function __get($p_sDataName)
    {
        return $this->get($p_sDataName);
    }

    /**
     * Retrieve data of the user
     *
     * null if no data exist
     *
     * @return mixed data
     */
    public function get($p_sDataName)
    {
        if(isset($this->_data->$p_sDataName))
        {
            return $this->_data->$p_sDataName;
        }
        else
        {
            return null;
        }
    }

    /**
    * Retrieve data of the user by calling a getter
    *
    * @throws Pbcore_Auth_Exception
    * @return mixed data
    */
    public function __call($p_sMethod, $p_sArguments)
    {
        $matches = array();

        //control that it's a getter
        if(preg_match('/^get(\w+?)?$/', $p_sMethod, $matches))
        {
            //get the name of the data searched
            $dataName = strtolower($matches[1][0]). substr($matches[1], 1);

            //if the data request exist we send the data
            if(isset($this->_data->$dataName))
            {
                return $this->_data->$dataName;
            }
        }

        throw new Pbcore_Auth_Exception('Unrecognized method "' . $p_sMethod . '()"');
    }
}
