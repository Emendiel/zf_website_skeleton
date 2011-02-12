<?php

class Mn2_Service_Facebook_Graph_User 
{
    /**
     * graph client
     * 
     * @var Mn2_Service_Facebook_Graph
     */
    protected $_graph;
    
    /**
     * data
     * 
     * @var array
     */
    protected $_data;
    
    /**
     * Constructor
     * @param Mn2_Service_Facebook_Graph $graph graph client
     * @param mixed|null $uid uid of the user (default null)
     */
    public function __construct(Mn2_Service_Facebook_Graph $graph, $uid = null)
    {
        $this->_uid   = $uid;
        $this->_graph = $graph;
    }
    
    /**
     * Set Data
     * @param array $data data
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        
        return $this;
    }
    
    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $result = $default;
        
        if (array_key_exists($name, $this->_data)) 
        {
            $result = $this->_data[$name];
        }
        
        return $result;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}