//Hack for Safari to accept cookies
(function() {

	if ($.browserDetect.browser == "Safari") {
		var fbCookie, fbSafariForm;

		fbCookie = $.cookie('fbs_' + websiteConfig.fb.appId);

		if (!fbCookie && websiteConfig.user.loggedIn) {
			fbSafariForm = document.createElement("form");
			fbSafariForm.action = location.protocol + '//' + location.host
					+ '/shared/redirect' + location_search + "&redirectTo="
					+ encodeURI(baseURL + "/" + location.pathname);
			fbSafariForm.method = "POST";
			document.body.appendChild(fbSafariForm);
			fbSafariForm.submit();
		}
	}
})();
