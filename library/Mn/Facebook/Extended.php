<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Facebook
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Facebook.php 21015 2010-02-11 01:56:02Z freak $
 */

require_once 'Facebook/facebook.php';

/**
 * Facebook Extended
 *
 * @category   Mn
 * @package    Mn_facebook
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */

class Mn_Facebook_extended extends Facebook
{
    protected $_config = array();
    
    protected static $_urlMap = array(
        "apps" => "http://apps.facebook.com/"
    );
    
    protected static $_requiredConfig = array(
        "appName",
        "appId",
        "secret"
    );

    /**
     * Initialize a Facebook Application.
     *
     * The configuration:
     * - appType: the application type (iframe, web)
     * - appPerms: comma separated list of requested extended perms to ask when authenticate
     * - appName: the application name
     * - appId: the application ID
     * - secret: the application secret
     * - cookie: (optional) boolean true to enable cookie support
     * - domain: (optional) domain for the cookie
     * - fileUpload: (optional) boolean indicating if file uploads are enabled
     *
     * @param Array $config the application configuration
     */
    public function __construct($config = array()) {
        
        $configMissing = array_diff(self::$_requiredConfig, array_keys($config));
        
        if(count($configMissing) > 0)
        {
            throw new Zend_Exception('Facebook configuration params are missing :' .print_r($configMissing, true));
        }
        
        $this->_config = $config;
        parent::__construct($config);
    }
    
    /**
     * Get Configuration options
     * @return array
     */
    public function getOptions(){
        return $this->_config;
    }
    
  /**
   * Get application URL
   *
   * @return String the URL of the application
   */
    public function getApplicationUrl() {
        return self::$_urlMap['apps'] . $this->_config['appName'];
    }
    
  /**
   * Is authenticated
   *
   * @return boolean true if authenticated
   */
    public function isAuthenticated()
    {
        return (boolean) $this->getSession();
    }
    
    public function authenticate($params = array())
    {
        if ( !$this->isAuthenticated() )
        {
            $loginConfig = array();
            
            if(isset($this->_config['type']) && $this->_config['type'] == 'iframe')
            {
                $request = (isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI']!='')) ? substr($_SERVER['REQUEST_URI'], 2) : null;
        
                $loginConfig['next'] = $this->getApplicationUrl() . $request;
                $loginConfig['cancel_url'] = $this->getApplicationUrl() . $request;
            }
            
            if(isset($this->_config['appParams']) && !empty($this->_config['appParams']))
            {
                $loginConfig['req_perms'] = $this->_config['appParams'];
            }
            
            $loginConfig = array_merge($loginConfig, $params);
                    
            $url = $this->getLoginUrl($loginConfig);
            $this->redirect($url);
        }
    }

    public function redirect($url)
    {
        if (preg_match('/^https?:\/\/([^\/]*\.)?facebook\.com(:\d+)?/i', $url))
        {
            // make sure facebook.com url's load in the full frame so that we don't
            // get a frame within a frame.
            echo "<script type=\"text/javascript\">\ntop.location.href = \"$url\";\n</script>";
        }
        else
        {
            header('Location: ' . $url);
        }

        exit;
    }


    /**
     * Make an FQL.QUERY call.
     *
     * @param String $query the query (required)
     * @param String $format optional response format (default XML)
     * @return the decoded response
     */
    public function fql($query)
    {
        $params['query']=$query;
        $params['format']='json';
        $params['locale']='en_US';

        $result = json_decode($this->jsonIntToStr($this->_oauthRequest(
        $this->getUrl('api', 'method/fql.query'),
        $params
        )), true);

        // results are returned, errors are thrown
        if (is_array($result) && isset($result['error']))
        {
            $e = new FacebookApiException($result);
            if ($e->getType() === 'OAuthException')
            {
                $this->setSession(null);
            }
            throw $e;
        }
        return $result;
    }


    /**
     * Get profile pic url.
     *
     * @param String $userID the User FaceBook ID (required), "me" or "/me" for the current user
     * @param String $type optional picture size : Supported types: small, normal, large, square (default square)
     * @return the picture url
     */
    public function getProfilePic($uid,$type="square") {
        $params['type'] = $type;

        if($uid=='me' || $uid=='/me') {
            $params['access_token'] = $this->getAccessToken();
        }

        $path = $uid.'/picture';

        $url = $this->getURL('graph', $path, $params);

        return $url;
    }

    public function getUserBasicInfos($uid='me') {

        if($this->user==null) {

            if (isset($_SESSION['user_basic_infos']) && $_SESSION['user_basic_infos']!=null && isset($_SESSION['user_basic_infos_updt']) && time()-$_SESSION['user_basic_infos_updt']<FB_UPDATE_DELAY) {
                $this->user = $_SESSION['user_basic_infos'];
            }
            else {
                try {
                    $this->user = $this->api($uid,array('locale'=>'en_US'));
                    $_SESSION['user_basic_infos'] = $this->user;
                    $_SESSION['user_basic_infos_updt'] = time();
                }
                catch(FacebookApiException $e) {
                    error_log("FacebookApiException ---> ".print_r($e,true));

                    if ($e->getType() === 'OAuthException') {
                        destroy_user_session();
                        $this->redirect(GOSSIP_SERVER_URL);
                        exit;
                    }
                    else {
                        displayerror($e);
                        exit;
                    }

                }
            }
        }

        return $this->user;
    }

    public function getUserFriendsIds($uid='me') {

        $ids=null;

        if (isset($_SESSION['user_friends_ids']) && $_SESSION['user_friends_ids']!=null && isset($_SESSION['user_friends_ids_updt']) && time()-$_SESSION['user_friends_ids_updt']<FB_UPDATE_DELAY) {
            $ids = $_SESSION['user_friends_ids'];
        }
        else {

            try {
                $user_friends = $this->api('me/friends');

                if(empty($user_friends) || !is_array($user_friends)) {
                    return null;
                }

                foreach($user_friends['data'] as $user_friend)
                {
                    $ids[$user_friend['id']] = $user_friend['id'];
                }

                $_SESSION['user_friends_ids'] = $ids;
                $_SESSION['user_friends_ids_updt'] = time();
            }
            catch(FacebookApiException $e) {
                error_log("FacebookApiException ---> ".print_r($e,true));

                if ($e->getType() === 'OAuthException') {
                    destroy_user_session();
                    $this->redirect(GOSSIP_SERVER_URL);
                    exit;
                }
                else {
                    displayerror($e);
                    exit;
                }

            }
        }

        return $ids;
    }

    public function getUserFriendsInfos($uid='me') {

        $fql_friends=null;

        $ids = $this->getUserFriendsIds();

        if(!is_array($ids)){
            return null;
        }

        if (isset($_SESSION['user_friends']) && $_SESSION['user_friends']!=null && isset($_SESSION['user_friends_updt']) && time()-$_SESSION['user_friends_updt']<FB_UPDATE_DELAY) {
            $fql_friends = $_SESSION['user_friends'];
        }
        else {
            try {
                $fql_friends    =   $this->fql('SELECT uid, sex, first_name, last_name, name FROM user WHERE uid IN('.implode(',', array_merge($ids, array(FB_USER))).') ORDER BY name ASC');

                if(empty($fql_friends)){
                    return null;
                }

                $_SESSION['user_friends'] = $fql_friends;
                $_SESSION['user_friends_updt'] = time();
            }
            catch(FacebookApiException $e) {
                error_log("FacebookApiException ---> ".print_r($e,true));

                if ($e->getType() === 'OAuthException') {
                    destroy_user_session();
                    $this->redirect(GOSSIP_SERVER_URL);
                    exit;
                }
                else {
                    displayerror($e);
                    exit;
                }

            }

        }

        return $fql_friends;
    }

    public function getCurrentUrl_ext() {

        $newUrl=FB_APP_URL; //Nouvelle adresse

        if (isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI']!=''))
        $newUrl.=substr($_SERVER['REQUEST_URI'],2);

        return $newUrl;
    }

    public function jsonIntToStr($json){
        $pattern = "/\"uid\"[ ]*:[ ]*([0-9]+)/";
        $replace = "\"uid\":\"$1\"";
        $new_json = preg_replace($pattern, $replace, $json);
        return $new_json;
    }
}

?>