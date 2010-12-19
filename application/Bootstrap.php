<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initialize the Cache Manager
     * Initializes the memcached cache into
     * the registry and returns the cache manager.
     *
     * @return Zend_Cache_Manager
     */
    protected function _initCachemanager()
    {
        $cachemanager = $this->getPluginResource('cachemanager')
                             ->init();
 
        //TODO: add a version number for memcache
        // fetch the current revision from svn and use it as a prefix
        // why: we do not want to restart memcached, or you will lose sessions.
        /*if (!$appVersion = apc_fetch('progsite_version')) {
            $dir = getcwd();
            chdir(dirname(__FILE__));
            $appVersion = filter_var(`svn info | grep "Revision"`, FILTER_SANITIZE_NUMBER_INT);
            chdir($dir);
            unset($dir);
            if (!$appVersion) {
                $appVersion = mt_rand(0, 99999); // simply handles an export instead of checkout
            }
            apc_store('progsite_version', $appVersion);
        }*/
 
        $memcached = $cachemanager->getCache('memcached');
        $memcached->setOption('cache_id_prefix', APPLICATION_ENV /*. '_' . $appVersion*/);
 
        return $cachemanager;
    }
 
    /**
     * Initialize the Session Id
     * This code initializes the session and then
     * will ensure that we force them into an id to
     * prevent session fixation / hijacking.
     *
     * @return void
     */
    protected function _initSessionId()
    {
        $this->bootstrap('session');
        $opts = $this->getOptions();
        if ('Mn_Session_SaveHandler_Cache' == $opts['resources']['session']['saveHandler']['class']) {
            $cache = $this->bootstrap('cachemanager')
                          ->getResource('cachemanager')
                          ->getCache('memcached'); 
            $cache = clone $cache;
            $cache->setOption('cache_id_prefix', APPLICATION_ENV);
 
            Zend_Session::getSaveHandler()->setCache($cache);
        }
        $defaultNamespace = new Zend_Session_Namespace();
        if (!isset($defaultNamespace->initialized)) {
            Zend_Session::regenerateId();
            $defaultNamespace->initialized = true;
        }
    }
}

