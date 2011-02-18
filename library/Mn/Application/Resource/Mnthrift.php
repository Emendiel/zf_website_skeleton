<?php
/**
 *
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 */

require_once 'Zend/Application/Resource/ResourceAbstract.php';

require_once 'Mn/Thrift.php';

/**
 */

/**
 * Thrift manager resource
 *
 * Example configuration:
 * <pre>
 *   resources.mnthrift.th1.path                 = "Thrift"
 *   resources.mnthrift.th1.namespace            = "myspace"
 *   resources.mnthrift.th1.service              = "myservice"
 *   resources.mnthrift.th1.socket.class         = "TSocket"
 *   resources.mnthrift.th1.socket.options.host  = "localhost"
 *   resources.mnthrift.th1.socket.options.port  = 9090
 *   resources.mnthrift.th1.transport.class      = "TBufferedTransport"
 *   resources.mnthrift.th1.default              = 1
 * </pre>
 *
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 */
class Mn_Application_Resource_Mnthrift extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Default registry Key
     *
     * @var string
     */
    const DEFAULT_REGISTRY_KEY = 'Mn_Thrift';

    /**
     * Associative array containing all configured thrift client's
     *
     * @var array
     */
    protected $_thrifts = array();

    /**
     * An instance of the default thrift client, if set
     *
     * @var null|Mn_Thrift
     */
    protected $_defaultThrift;
    
    /**
     * Registry key
     *
     * @var string
     */
    protected $_registryKey;

    /**
     * Initialize the Thrift Connections (instances of Mn_Thrift)
     *
     * @return Mn_Application_Resource_Mnthrift
     */
    public function init()
    {
        $options = $this->getOptions();
        
        $this->_registryKey = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
        ? $options['registry_key']
        : self::DEFAULT_REGISTRY_KEY;
        
        unset($options['registry_key']);

        foreach ($options as $id => $params)
        {
            $default = (int) (isset($params['default']) && $params['default']);
            unset($params['default']);

            $this->_thrifts[$id] = new Mn_Thrift($params);

            if ($default) {
                $this->_setDefault($this->_thrifts[$id]);
            }
        }

        //set in registry
        Zend_Registry::set($this->_registryKey, $this);

        return $this;
    }

    /**
     * Determine if the given thrift(identifier) is the default thrift.
     *
     * @param  string|Mn_Thrift $thrift The thrift to determine whether it's set as default
     * @return boolean True if the given parameter is configured as default. False otherwise
     */
    public function isDefault($thrift)
    {
        if(!$thrift instanceof Mn_Thrift) {
            $thrift = $this->getThrift($thrift);
        }

        return $thrift === $this->_defaultThrift;
    }

    /**
     * Retrieve the specified thrift connection
     *
     * @param  null|string $thriftKey The thrift to retrieve. Null to retrieve the default connection
     * @return Mn_Thrift
     * @throws Zend_Application_Resource_Exception if the given parameter could not be found
     */
    public function getThrift($thriftKey = null)
    {
        if ($thriftKey === null) {
            return $this->getDefaultThrift();
        }

        if (isset($this->_thrifts[$thriftKey])) {
            return $this->_thrifts[$thriftKey];
        }

        throw new Zend_Application_Resource_Exception(
            'A Thrift was tried to retrieve, but was not configured'
            );
    }

    /**
     * Get the default thrift connection
     *
     * @return null|Mn_Thrift
     */
    public function getDefaultThrift()
    {
        if ($this->_defaultThrift !== null) {
            return $this->_defaultThrift;
        }

        return reset($this->_thrifts); // Return first thrist in db pool

    }

    /**
     * Set the default thrift adapter
     *
     * @var Mn_Thrift $thrift Thrift to set as default
     */
    protected function _setDefault(Mn_Thrift $thrift)
    {
        $this->_defaultThrift = $thrift;
    }
}
