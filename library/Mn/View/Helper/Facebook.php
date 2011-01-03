<?php

class Mn_View_Helper_Facebook extends Zend_View_Helper_Abstract
{
	public function facebook(){
    return $this;
	}

	public function getJsConnect(){
		 
		 $sLocale = Zend_Registry::get('Zend_Locale');
		 
		 $sFbAppId = '160774024217';
		 
  	 $sScript = "
  	 
  	 <script language='JavaScript' type='text/javascript'>
  	 
  	 var fbRoot, e;
  	 fbRoot = document.createElement('div');
     fbRoot.id = 'fb-root';
     document.body.appendChild(fbRoot);

     e = document.createElement('script');
     e.async = true;
     e.src = document.location.protocol + '//connect.facebook.net/" . $sLocale . "/all.js';
     fbRoot.appendChild(e);
     
     window.fbAsyncInit = function fbAsyncInit() {

        FB.init({
          appId: '" . $sFbAppId . "',
          status: true,
          cookie: true,
          xfbml: true});

        FB.Event.subscribe('auth.sessionChange', function(response) {
           /** if (response.session) {
              $.ajax({
                  type     : 'GET',
                  url      : '/default/index/session',
                  dataType : 'json',
                  cache    : false,
                  success  : function (result) {
                      if (result.user && result.user['facebook_connect.facebook_user_id'] && result.user['facebook_connect.facebook_user_id'] == response.session.uid) {
                          return ;
                      } else {
                          document.location = '/auth/auth/fblogin?goback='+document.location.href;
                      }
                  },
                  failure  : function (result) {}
              });
            } else {
              document.location = '/auth/auth/logout';
            }**/
        });
    }
     
     </script>
     
     ";
  	 
  	 return $sScript;
	}
}