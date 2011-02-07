//Hack for Safari to accept cookies
(function() {

	if ($.browserDetect.browser == "Safari") {
		var fbCookie, fbSafariForm;

		fbCookie = $.cookie('fbs_' + config.fb.app_id);

		if (!fbCookie && config.user.logged_in) {
			fbSafariForm = document.createElement("form");
			fbSafariForm.action = location.protocol + '//' + location.host
					+ '/redirect' + location_search + "&return_to="
					+ encodeURI(baseURL + "/" + location.pathname);
			fbSafariForm.method = "POST";
			document.body.appendChild(fbSafariForm);
			fbSafariForm.submit();
		}
	}
})();
