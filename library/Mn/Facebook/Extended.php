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

class Mn_Facebook_Extended extends Facebook
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
         * Set permissions.
         *
         * @param array $perms list of permission
         * @return this
         */
        public function setPerms($perms) {
            $this->_config['permissions'] = $perms;
            return $this;
        }

        /**
         * Get the permissions
         *
         * @return array list of permission
         */
        public function getPerms() {
            return isset($this->_config['permissions']) ? $this->_config['permissions'] : array();
        }


        /**
         * Require login
         *
         * @param string $url
         * @return boolean
         */
        public function requireLogin($url=null)
        {
            if ( !$this->getSession() )
            {
                if (empty($url))
                {
                    $url = $this->getLoginUrl(array(
                                                    'req_perms'  => $this->getPerms()));
                }
                $this->redirect($url);
            }

            return true;
        }

        /**
         * Redirect to a url in js if it's a facebook url with http location otherwise
         *
         * @param string $url
         */
        public function redirect($url)
        {
            if (preg_match('/^https?:\/\/([^\/]*\.)?facebook\.com(:\d+)?/i', $url)) {
                // make sure facebook.com url's load in the full frame so that we don't
                // get a frame within a frame.
                echo '<script type="text/javascript">top.location.href = "'.$url.'";</script>';
            } else {
                header('Location: ' . $url);
            }
            exit;
        }

        /**
         * Make an FQL.QUERY call.
         *
         * @param String $query the query (required)
         * @param String $format optional response format (default XML)
         * @param String $locale optional locale (default en_US)
         * @return the decoded response
         */
        public function fql($query, $format = 'json', $locale = 'en_US')
        {
            $params = array();

            $params['query']  = $query;
            $params['format'] = $format;
            $params['locale'] = $locale;
             
            $jsonResult = $this->jsonUidToStr($this->_oauthRequest($this->getUrl('api', 'method/fql.query'), $params));

            $result = Zend_Json::decode($jsonResult);

            // results are returned, errors are thrown
            if (is_array($result) && isset($result['error']))
            {
                $e = new FacebookApiException($result);

                if ($e->getType() === 'OAuthException') {
                    $this->setSession(null);
                }

                throw $e;
            }
            return $result;
        }


        /**
         * Hack to support long uid (convert to string)
         *
         * @param String $json json
         * @return string json
         */
        protected function jsonUidToStr($json){
            $pattern = "/\"uid\"[ ]*:[ ]*([0-9]+)/";
            $replace = "\"uid\":\"$1\"";
            $new_json = preg_replace($pattern, $replace, $json);
            return $new_json;
        }

        /**
         * Get profile pic url.
         *
         * @param String $userID optional User FaceBook ID, "me" or "/me" for the current user (default me)
         * @param String $type optional picture size : small, normal, large, square (default square)
         * @return the picture url
         */
        public function getProfilePic($uid = 'me', $type = 'square')
        {
            $params         = array();
            $params['type'] = $type;

            if( $uid == 'me' || $uid == '/me')
            {
                $params['access_token'] = $this->getAccessToken();
            }

            $path = $uid.'/picture';

            return $this->getURL('graph', $path, $params);
        }

        /**
         * Get User Information
         *
         * @param string $uid
         * @param array $params
         * @return array
         */
        public function getUserInfo($uid = 'me' , $params = array('locale' => 'en_US'))
        {
            if(isset($this->user[$uid]) && !empty($this->user[$uid]))
            {
                return $this->user[$uid];
            }

            $this->user[$uid] = $this->api($uid, $params);

            return $this->user;
        }

        /**
         * Get list of friend uid
         *
         * @param string $uid
         * @return array
         */
        public function getUserFriends($uid = 'me')
        {
            if(!empty($this->friends[$uid]))
            {
                return $this->friends[$uid];
            }

            $this->friends[$uid] = $this->api('me/friends');

            return $this->friends;
        }

        
        //!\\ HACK use of urldecode on cookie value, bug PHP FB API
        /**
         * Get the session object. This will automatically look for a signed session
         * sent via the signed_request, Cookie or Query Parameters if needed.
         *
         * @return Array the session
         */
        public function getSession() {
            if (!$this->sessionLoaded) {
                $session = null;
                $write_cookie = true;

                // try loading session from signed_request in $_REQUEST
                $signedRequest = $this->getSignedRequest();
                if ($signedRequest) {
                    // sig is good, use the signedRequest
                    $session = $this->createSessionFromSignedRequest($signedRequest);
                }

                // try loading session from $_REQUEST
                if (!$session && isset($_REQUEST['session'])) {
                    $session = json_decode(
                    get_magic_quotes_gpc()
                    ? stripslashes($_REQUEST['session'])
                    : $_REQUEST['session'],
                    true
                    );
                    $session = $this->validateSessionObject($session);
                }

                // try loading session from cookie if necessary
                if (!$session && $this->useCookieSupport()) {
                    $cookieName = $this->getSessionCookieName();
                    if (isset($_COOKIE[$cookieName])) {
                        $session = array();
                        parse_str(trim(
                        get_magic_quotes_gpc()
                        ? urldecode(stripslashes($_COOKIE[$cookieName]))
                        : urldecode($_COOKIE[$cookieName]),
            '"'
            ), $session);
            $session = $this->validateSessionObject($session);
            // write only if we need to delete a invalid session cookie
            $write_cookie = empty($session);
                    }
                }

                $this->setSession($session, $write_cookie);
            }

            return $this->session;
        }
}

?>
