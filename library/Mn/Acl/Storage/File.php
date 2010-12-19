<?php

/**
 *
 * @category   Mn
 * @package    Mn_Acl
 * @subpackage Storage
 */

/**
 * @category   Mn
 * @package    MN_Acl
 * @subpackage Storage
 */
class Mn_Acl_Storage_File implements Mn_Acl_Storage_Interface
{

    /**
     * Path to store the file
     *
     * @var string
     */
    protected $_path;
    
    /**
     * object to proxy file storage
     *
     * @var Zend_Acl
     */
    protected $_data = null;

    /**
     * Sets file storage options
     *
     * @param  string $path
     * @return void
     */
    public function __construct($path)
    {
        $this->_path = $path;
    }

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Defined by Mn_Acl_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !file_exists($this->_path) || filesize($this->_path) === 0;
    }

    /**
     * Defined by Mn_Acl_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
        if(empty($this->_data) && file_exists($this->_path))
        {
             $data = file_get_contents($this->getPath());
             
             if($data === false)
             {
                 throw new Mn_Acl_Storage_Exception("Unable to read in ".$this->getPath());
             }
             
             $data = unserialize($data);
             
             if(!($data instanceof Zend_Acl))
             {
                 throw new Mn_Acl_Storage_Exception("Not a Zend_Acl object store in ".$this->getPath());
             }
             
             $this->_data = $data;
        }
        
        
        return $this->_data;
    }

    /**
     * Defined by Mn_Acl_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write(Zend_Acl $contents)
    {
        if(file_put_contents($this->getPath(), serialize ($contents)) === false)
        {
            throw new Mn_Acl_Storage_Exception("Unable to write in ".$this->getPath());
        }
        
        $this->_data = $contents;
    }

    /**
     * Defined by Mn_Acl_Storage_Interface
     *
     * @return void
     */
    public function clear()
    {
        if(@unlink($this->getPath()) === false)
        {
            throw new Mn_Acl_Storage_Exception("Unable to delete ".$this->getPath());
        }
        
        $this->data = null;
        
    }
}
