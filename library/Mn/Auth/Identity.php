<?php

class Mn_Auth_Identity
{

    /**
     * Auth Type
     *
     * @var string
     */
    protected $_authType = '';

    /**
     * Logged In
     *
     * @var boolean
     */
    protected $_loggedIn = false;

    /**
     * Data of the identity
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Constructor
     *
     * @param string $authType
     */
    public function __construct($authType)
    {
        $this->_authType = $authType;
    }

    /**
     * Set data
     * 
     * @param array $oData
     */
    public function setData($oData)
    {
        $this->_data = $oData;
    }

    /**
     * Clean user data
     *
     * Deleting userRoleTable
     *
     */
    public function clean()
    {
        $this->_data = array();
    }
    
    /**
     * Get Auth type
     * 
     * @return string
     */
    public function getAuthType()
    {
        return $this->_authType;
    }

    /**
     * Is logged in
     *
     * @return boolean
     */
    public function isLogged()
    {
        return $this->_loggedIn;
    }

    /**
     * Refresh data
     *
     */
    public function refresh()
    {
        //TODO
    }

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
        return (isset($this->_data[$p_sDataName])) ? $this->_data[$p_sDataName] : null;
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

        //check if it's a getter
        if(preg_match('/^get(\w+?)?$/', $p_sMethod, $matches))
        {
            //get the name of the data searched
            $dataName = strtolower($matches[1][0]). substr($matches[1], 1);

            return $this->get($dataName);
        }

        throw new Pbcore_Auth_Exception('Unrecognized method "' . $p_sMethod . '()"');
    }

    /**
     * To Array
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
}
