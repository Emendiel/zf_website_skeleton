<?php

class Mn2_Service_Facebook {

	/**
	 * Default options
	 *
	 * @var array
	 */
    protected $_defaultOptions = array();
    
    protected $_graph = null;
    
	/**
	 * Options
	 *
	 * @var Zend_Config|null
	 */
    protected $_options = null;
    
    public function __construct($options = array()) 
    {
        $this->setOptions($options);
        
        $this->_graph = new Mn2_Service_Graph();
    }

	/**
	 * Set configuration options
	 *
	 * @param Zend_Config|array $options options
	 * @return $this
	 * @throws Exception
	 */
    public function setOptions($options)
    {
        if(is_array($options)) 
        {
            $options = new Zend_Config($options);
        }
        elseif(!($options instanceof Zend_Config))
        {
            throw new Exception('Configuration options must be an array or a Zend_Config object');
        }
        
        $this->_options = new Zend_Config($this->_defaultOptions, true);
        $this->_options->merge($options);
        
        return $this;
    }
    
	/**
	 * Get configuration options
	 *
	 * @return Zend_Config
	 */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if($name = 'graph')
        {
            return $this->_graph;
        }
        
        throw new Exception(__CLASS__ . '->' . $name .' not exist');
    }
}