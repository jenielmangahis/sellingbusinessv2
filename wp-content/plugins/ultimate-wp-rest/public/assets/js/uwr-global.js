/*jshint esversion: 6 */
readyDOM(() => {
	jQuery(document).ready($ => {
		if (!uwrData) {
			console.warn("CLIENT: uwrData isn't set.");
			return;
		}

		console.log(uwrData);

		// Debugger
		if (uwrData.debugger.enable != 1) {
			console.log('API: debugger is disable.');
			console.log = function () {};
			console.warn = function () {};
			console.error = function () {};
			console.table = function () {};
		}
	});
}, false);

function readyDOM(callback) {
	// in case the document is already rendered
	if (document.readyState != 'loading') callback();
	// modern browsers
	else if (document.addEventListener) document.addEventListener('DOMContentLoaded', callback);
	// IE <= 8
	else document.attachEvent('onreadystatechange', function () {
		if (document.readyState == 'complete') callback();
	});
}

function debounce(func, timeout, immediate = false) {
	return function () {
		var context = this,
			args = arguments;
		var later = function () {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, timeout);
		if (callNow) func.apply(context, args);
	};
}