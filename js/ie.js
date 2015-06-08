/* ---------------------------------------------------------------------- */
/*	Browser Compatibility Check
 /* ---------------------------------------------------------------------- */
if ($.browser.msie) {
    var bResult = document.implementation.hasFeature("org.w3c.svg", "1.0");
    if (parseInt($.browser.version, 10) <= 7 && !bResult) {
        var url = document.URL;
        if (url.indexOf('?') >= 0) {
            url = url + '&unsupported=true';
        } else {
            url = url + '?unsupported=true';
        }
        window.location.href = url;
    }
}

