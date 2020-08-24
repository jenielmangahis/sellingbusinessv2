function domReady(callback) {
    // Mozilla, Opera and Webkit
    if (document.addEventListener) {
        document.addEventListener("DOMContentLoaded", callback, false);
        // If IE event model is used
    } else if (document.attachEvent) {
        document.attachEvent("onreadystatechange", function() {
            if (document.readyState === "complete" ) {
                callback();
            }
        });
        // A fallback to window.onload, that will always work
    } else {
        var oldOnload = window.onload;
        window.onload = function () {
            oldOnload && oldOnload();
            callback();
        }
    }
}

function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
	var d = new Date();
	d.setTime(d.getTime() + expires*1000);
	expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
	options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for(var propName in options) {
	updatedCookie += "; " + propName;
	var propValue = options[propName];
	if (propValue !== true) {
	  updatedCookie += "=" + propValue;
	 }
  }

  document.cookie = updatedCookie;
}

// возвращает cookie с именем name, если есть, если нет, то undefined
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
	"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

function deleteCookie( name, path, domain ) {
  if( getCookie( name ) ) {
    document.cookie = name + "=" +
      ((path) ? ";path="+path:"")+
      ((domain)?";domain="+domain:"") +
      ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
  }
}