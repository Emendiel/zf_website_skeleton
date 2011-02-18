<?php
/**
 *
 * @category   Mn
 * @package    Mn_Thrift
 */

/**
 * Thrift manager
 *
 * @category   Mn
 * @package    Mn_Thrift
 */
class Mn_Thrift {

	/**
	 * Default options
	 *
	 * @var array
	 */
	protected $_defaultOptions = array(
      'path'      => 'Thrift',
      'namespace' => null,
      'service'   => null,
      'socket'    => array(
                        'class' => 'TSocket',
                        'options' => null),
      'transport' => array(
                        'class'   => 'TBufferedTransport',
                        'options' => null),
	  'protocol'  => array(
	                     'class' => 'TBinaryProtocol',
	                     'options' => null)
	);


	/**
	 * Options
	 *
	 * @var Zend_Config|null
	 */
	protected $_options = null;

	/**
	 * Thrift Client Object
	 *
	 * @var mixed
	 */
	protected $_client = null;

	/**
	 * Thrift Transport Object
	 *
	 * @var mixed
	 */
	protected $_transport = null;
	
	/**
	 * Thrift socket Object
	 *
	 * @var mixed
	 */
	protected $_socket = null;
	
	/**
	 * Thrift protocol Object
	 *
	 * @var mixed
	 */
	protected $_protocol = null;

	/**
	 * Construct Mn_Thrift
	 *
	 * @param Zend_Config|array $options options
	 */
	public function __construct($options)
	{
		if(!empty($options))
		{
			$this->setOptions($options);
		}
	}

	/**
	 * Set Thrift option
	 *
	 * @param Zend_Config|array $options options
	 * @return Mn_Thrift
	 * @throws Mn_Thrift_Exception
	 */
	public function setOptions($options)
	{
		$this->_options = new Zend_Config($this->_defaultOptions, true);

		if(!($options instanceof Zend_Config))
		{
			if(is_array($options))
			{
				$options = new Zend_Config($options);
			}
			else {
				throw new Mn_Thrift_Exception('options argument must be a Zend_Config object or an array');
			}
		}

		$this->_options = $this->_options->merge($options);

		return $this;
	}

	/**
	 * Get thrift option
	 *
	 * @return Zend_Config|null
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Get Thrift Client
	 *
	 * @throws Mn_Thrift_Exception
	 */
	protected function _getClient()
	{
		if($this->_client)
		{
			return $this->_client;
		}

		if(empty($this->_options) || empty($this->_options->namespace) || empty($this->_options->service))
		{
			throw new Mn_Thrift_Exception('Thrift options namespace, service are needed');
		}

		require_once $this->_options->path.'/Thrift.php';
		require_once $this->_options->path.'/packages/'.$this->_options->namespace.'/'.$this->_options->service.'.php';

		$protocol = $this->_getProtocol();

		try {
			$clientClass = $this->_options->service . 'Client';
			$this->_client = new $clientClass($protocol);
		} catch (Exception $e)
		{
			throw new Mn_Thrift_Exception('Thrift Client Exception : '.$e);
		}

		return $this->_client;
	}

	/**
	 * Get Socket Object
	 *
	 * @throws Mn_Thrift_Exception
	 * @return mixed
	 */
	protected function _getSocket()
	{
		if(empty($this->_options) || empty($this->_options->socket))
		{
			throw new Mn_Thrift_Exception('Thrift options socket is needed');
		}

		if(empty($this->_options->socket->class))
		{
			throw new Mn_Thrift_Exception('Thrift options socket class is needed');
		}

		require_once $this->_options->path.'/transport/' . $this->_options->socket->class . '.php';

		switch($this->_options->socket->class)
		{
			case 'TSocket':
				$host    = (isset($this->_options->socket->options->host))    ? $this->_options->socket->options->host    : 'localhost';
				$port    = (isset($this->_options->socket->options->port))    ? $this->_options->socket->options->port    : 9090;
				$persist = (isset($this->_options->socket->options->persist)) ? $this->_options->socket->options->persist : false;

				try{
					$socket = new TSocket($host, $port, $persist);
				} catch (Exception $e)
				{
					throw new Mn_Thrift_Exception('Thrift Socket Exception : '.$e);
				}
				break;

			default:
				throw new Mn_Thrift_Exception('Thrift Socket class ' . $this->_options->socket->class . 'is note managed yet');
				break;
		}

		return $socket;
	}
	
	/**
	 * Has transport
	 * 
	 * @return boolean
	 */
	protected function _hasTransport()
	{
	    return !empty($this->_transport);
	}

	/**
	 * Get Transport
	 *
	 * @throws Mn_Thrift_Exception
	 * @return mixed
	 */
	protected function _getTransport()
	{
		if($this->_transport){
			return $this->_transport;
		}

		$socket = $this->_getSocket();

		if(empty($this->_options->transport))
		{
			throw new Mn_Thrift_Exception('Thrift options transport is needed');
		}

		if(empty($this->_options->transport->class))
		{
			throw new Mn_Thrift_Exception('Thrift options transport class is needed');
		}

		require_once $this->_options->path.'/transport/' . $this->_options->transport->class . '.php';

		switch($this->_options->transport->class)
		{
			case 'TBufferedTransport':
				$rBufSize = (isset($this->_options->transport->options->rBufSize)) ? $this->_options->transport->options->rBufSize : 1024;
				$wBufSize = (isset($this->_options->transport->options->wBufSize)) ? $this->_options->transport->options->wBufSize : 1024;

				try {
					$this->_transport = new TBufferedTransport($socket, $rBufSize, $wBufSize);
				} catch (Exception $e)
				{
					throw new Mn_Thrift_Exception('Thrift Transport Exception : '.$e);
				}
				break;

			default:
				throw new Mn_Thrift_Exception('Thrift transport class ' . $this->_options->transport->class . 'is note managed yet');
				break;
		}

		return $this->_transport;
	}
	
	/**
	 * Get Protocol
	 *
	 * @throws Mn_Thrift_Exception
	 * @return mixed
	 */
	protected function _getProtocol()
	{
		if($this->_protocol){
			return $this->_protocol;
		}

		$transport = $this->_getTransport();

		if(empty($this->_options->protocol))
		{
			throw new Mn_Thrift_Exception('Thrift options protocol is needed');
		}

		if(empty($this->_options->protocol->class))
		{
			throw new Mn_Thrift_Exception('Thrift options protocol class is needed');
		}

		require_once $this->_options->path.'/protocol/' . $this->_options->protocol->class . '.php';

		switch($this->_options->protocol->class)
		{
			case 'TBinaryProtocol':
				$strictRead  = (isset($this->_options->protocol->options->strictRead)) ? $this->_options->protocol->options->strictRead : false;
				$strictWrite = (isset($this->_options->protocol->options->strictWrite)) ? $this->_options->protocol->options->strictWrite : true;

				try {
					$this->_protocol = new TBinaryProtocol($transport, $strictRead, $strictWrite);
				} catch (Exception $e)
				{
					throw new Mn_Thrift_Exception('Thrift Protocol Exception : '.$e);
				}
				break;

			default:
				throw new Mn_Thrift_Exception('Thrift protocol class ' . $this->_options->protocol->class . 'is note managed yet');
				break;
		}

		return $this->_protocol;
	}

	/**
	 * Proxy to call methods of the Thrift Client
	 *
	 * @param string $name name of the method
	 * @param array $arguments arguments
	 * @throws Mn_Thrift_Exception, mixed
	 */
	public function __call($name, $arguments)
	{
		$client = $this->_getClient();

		if(!$this->_getTransport()->isOpen())
		{
			$this->_getTransport()->open();
		}

		if(!is_callable(array($client, $name))){
			throw new Mn_Thrift_Exception('Thrift client method '. get_class($client) . '->' . $name .' doesn\'t exist');
		}

		try{
			return call_user_func_array(array($client, $name), $arguments);
		} catch (TTransportException $e)
		{
			throw new Mn_Thrift_Exception('Thrift Transport Exception : '.$e);
		} catch(TProtocolException $e){
			throw new Mn_Thrift_Exception('Thrift Protocol Exception : '.$e);
		}
	}

	/**
	 * Close connection
	 */
	public function close()
	{
		if($this->_hasTransport() && $this->_getTransport()->isOpen())
		{
			$this->_getTransport()->close();
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct()
	{
		$this->close();
	}
}