/*jshint esversion: 6 */
readyDOM(() => {
  jQuery(document).ready($ => {
    pwpFunction.sendRequestAPI = (e, args) => {
      let apiName = $(args.api).val();
      let method = $(args.method).val();
      let data = method === 'GET' ? '' : $(args.data).val();

      try {
        data = JSON.parse(data);
      } catch (error) {
        console.log(error);
      }

      console.log(method, pwpData.api.url + '/' + apiName, data);
      let response = {};

      $.ajax({
        method: method,
        url: pwpData.api.url + '/' + apiName,
        data: data,
        type: 'text',
        beforeSend: function (xhr) {
          if(pwpData.api.nonce){
            xhr.setRequestHeader('X-WP-Nonce', pwpData.api.nonce);
          }          
        },
      }).always(data => {
        response = 'responseJSON' in data ? data.responseJSON : data;

        try {
          response = JSON.parse(response);
        } catch (e) {
          console.log(e);
        }

        console.log('api response', response);

        pwpFunction.showModal('Done! View data in Console end Network.');
      });
    };
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