<?php
/**
 *
 * @category   Mn
 * @package    Mn_Session
 * @version    $Id$
 */

/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * Mn_Session_SaveHandler_Cache
 *

 * @category   Mn
 * @package    Mn_Session
 * @subpackage SaveHandler
 */
class Mn_Session_SaveHandler_Cache implements Zend_Session_SaveHandler_Interface
{

    /**
     * Zend Cache
     * @var Zend_Cache
     */
    protected $_cache;

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        Zend_Session::writeClose();
    }

    /**
     * Set Cache
     *
     * @param Zend_Cache_Core $cache
     * @return Zend_Session_SaveHandler_Cache
     */
    public function setCache(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Get Cache
     *-
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Open Session
     *
     * @param string $save_path
     * @param string $name
     * @return boolean
     */
    public function open($save_path, $name)
    {
        $this->_sessionSavePath = $save_path;
        $this->_sessionName     = $name;

        return true;
    }

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        if (!$data = $this->_cache->load($id)) {
            return null;
        }
        return $data;
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     * @return boolean
     */
    public function write($id, $data)
    {
        return $this->_cache->save(
        $data,
        $id,
        array(),
        Zend_Session::getOptions('gc_maxlifetime')
        );
    }

    /**
     * Destroy session
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->_cache->remove($id);
    }

    /**
     * Garbage Collection
     *
     * @param int $maxlifetime
     * @return true
     */
    public function gc($maxlifetime)
    {
        return true;
    }

}