/*jshint esversion: 6 */
var pwpFunction;
readyDOM(() => {

  jQuery(document).ready($ => {
    if (!pwpData) {
      console.warn("CLIENT: pwpData isn't set.");
      return;
    }

    //console.log(pwpData);

    pwpFunction = {
      runAdminAjax: (e, args) => {
        console.log(args, 'arguments callback');

        let aOptions = args.data;
        var response = {};

        if (!args.go) {
          response.message = pwpData.translate.ajaxError;
          return response;
        }

        $.post({
          url: pwpData.ajax.url,
          dataType: 'json',
          data: {
            action: 'admin_ajax',
            go: args.go,
            options: aOptions
          }
        }).always(data => {
          response = 'responseJSON' in data ? data.responseJSON : data;
          if (typeof pwpFunction[args.callback] === 'function') {
            pwpFunction[args.callback](response);
          }
          return response;
        });
      },
      setRangeValueFormSlider: (e, elm) => {
        let that;
        if (typeof elm !== 'undefined') {
          that = $(elm);
        } else if (typeof e.currentTarget !== 'undefined') {
          that = $(e.currentTarget);
        } else if (typeof this !== 'undefined') {
          that = $(this);
        } else {
          return false;
        }

        let value = that.val();
        let textValue = that.parent().next();
        if (that.attr('istime') !== undefined) {
          textValue.css('width', 'auto');
          textValue.text(toFormatDate(value));
        } else {
          textValue.text(value);
        }
      },
      setInputValueFormSelect: (e, elm) => {
        let that = e.currentTarget ? e.currentTarget : e;
        let label = $(that).find('option:selected').closest('optgroup').prop('label');
        $(elm).val(label);
      },
      showElementFormSelect: (e, args) => {
        //console.log(args);
        let that = e.currentTarget ? e.currentTarget : e;
        let selector = args[1];
        let value = $(that).find('option:selected').val();

        if (value !== args[0]) {
          $(selector).hide();
        } else {
          $(selector).show();
        }

      },
      showModal: response => {
        let html = '';
        console.log('showModal', response);
        if (typeof response === 'object') {
          html = response.success ? response.message : print_r(response);
        } else {
          html = response;
        }

        M.toast({
          html: html
        });
      },

      initEvent: () => {
        // Handle tab event click
        $('.tabs').find('li').map((i, element) => {
          let aCallback = $(element).data('onclick');
          if (typeof aCallback !== 'undefined') {
            $(element).removeData('onclick');
            let funcName = aCallback[0];
            let args = aCallback[1];

            if (typeof pwpFunction[funcName] === 'function') {
              $(element).click(function (e) {
                console.log('Onclick form tabs', funcName, args);
                e.preventDefault();
                pwpFunction[funcName](args);
              });
            }
          }
        });

        $('.tabs-content').find('*').map((i, element) => {
          // Handle content element event click
          let aCallback = $(element).data('onclick');
          if (typeof aCallback !== 'undefined') {
            $(element).removeData('onclick');
            let funcName = aCallback[0];
            let args = aCallback[1];
            if (typeof pwpFunction[funcName] === 'function') {
              $(element).click(function (e) {
                //console.log('Onclick form content tab', funcName, args);
                e.preventDefault();
                pwpFunction[funcName](e, args);
              });
            }
          }

          // Handle content element event change
          aCallback = $(element).data('onchange');
          if (typeof aCallback !== 'undefined') {
            $(element).removeData('onchange');
            let funcName = aCallback[0];
            let args = aCallback[1];

            if (typeof pwpFunction[funcName] === 'function') {
              $(element).change(function (e) {
                //console.log('Onchange form section', funcName, args);
                pwpFunction[funcName](e, args);
              });
            }
          }

          // Handle content table event callback, each table corresponds to a wp section
          aCallback = $(element).data('onload');
          if (typeof aCallback !== 'undefined') {
            $(element).removeData('onload');
            let funcName = aCallback[0];
            let args = aCallback[1];

            if (typeof pwpFunction[funcName] === 'function') {
              //console.log('Onload form section', funcName, args);
              pwpFunction[funcName](element, args);
            }
          }
        });
      }
    };

    // Init Event Element
    setTimeout(function () {
      pwpFunction.initEvent();
    }, 500);

    // Materialize Tab
    $('.tabs').tabs({
      swipeable: false,
      duration: 200
    });

    // Remember selected tab
    $('.tabs').on('click', 'a', e => {
      let hash = e.currentTarget.hash.substr(1);
      Cookies.set('tab_setting', hash, {
        expires: 7,
        path: '/wp-admin'
      });
    });

    // WP Color picker
    if ($('input[choose-color]').length > 0) {
      $('input[choose-color]').iris();
    }

    // Materialize Modal
    $('.modal').modal();

    // Materialize Tooltip
    $('.tooltip').tooltip();

    // Materialize Select
    let mSelect = $('select').formSelect();

    // Materialize Collapsible
    $('.collapsible').collapsible();

    // Materialize Range
    $('input[type=range]').map(pwpFunction.setRangeValueFormSlider);
    $('input[type=range]').on('change mousemove', pwpFunction.setRangeValueFormSlider);

    // Materialize Switch
    $('.lever').on('click', function (e) {
      var curInput = $(e.currentTarget).siblings('input')[0];
      // if input checked is switch to off 

      console.log(curInput);
      $(curInput).val(curInput.checked ? 0 : 1);
    });

    // Materialize Chip
    var optionsMaterializeChip = {
      onChipAdd: function (e) {
        // Update input value
        let result = e[0].M_Chips.chipsData.map(chip => chip.tag);
        let inputTarget = $('input[name="' + e[0].id + '"]');
        inputTarget.val(JSON.stringify(result));

        // Add tooltip
        addChipTooltip(chip);
      },
      onChipDelete: function (e) {
        // Update input value
        let result = e[0].M_Chips.chipsData.map(chip => chip.tag);
        let inputTarget = $('input[name="' + e[0].id + '"]');
        inputTarget.val(JSON.stringify(result));
      }
    };

    for (var id in pwpData.settings) {
      if (pwpData.settings[id].type === 'chips') {
        optionsMaterializeChip = Object.assign({}, optionsMaterializeChip, pwpData.settings[id]);
        let instance = $('#' + id).chips(optionsMaterializeChip);

        // Add tooltip
        if (typeof instance[0] !== 'undefined') {
          instance[0].M_Chips.$chips.map(addChipTooltip);
        }
      }
    }

    function addChipTooltip(chip) {
      var chipText = $(chip).clone().children().remove().end().text();
      $(chip).tooltip({
        html: chipText,
        position: 'top'
      });
      let trimmedChipText = chipText.substring(0, 30);
      trimmedChipText += chipText.length > 30 ? '...' : '';
      $(chip).html(trimmedChipText + '<i class="close material-icons">close</i>');
    }

    // Textarea format
    $('textarea').each(function () {
      let format = this.attributes.format.value;
      if (format === 'jsonp') {
        let value = $(this).val();
        try {
          let json = JSON.parse(value);
          let jsonp = JSON.stringify(json, undefined, 2);
          $(this).val(jsonp);
        } catch (error) {
          return error;
        }
      }
    });

    // Media Uploader
    var mediaUploader, curImage;
    $('img[choose-image]').click(function (e) {
      e.preventDefault();
      curImage = $(this);
      console.log(curImage);

      //  If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      //  Extend the wp.media object
      mediaUploader = wp.media({
        title: pwpData.translate.chooseImage,
        button: {
          text: pwpData.translate.chooseImage
        },
        multiple: false
      });
      //  When a file is selected, grab the URL and set it as the text field's value
      mediaUploader.on('select', function () {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        curImage.attr("src", attachment.url);
        $('#' + curImage.attr("data")).attr("value", attachment.url);
      });
      //  Open the uploader dialog
      mediaUploader.open();
    });

    //  Can add button get token from server by js instead of callback in settings_field // 

    // Submit Ajax
    $('#optionsForm').submit(e => {

      e.preventDefault();

      // Get all options
      var aOptions = $(e.currentTarget).serializeArray();

      let aOptionName = [];
      $.map(aOptions, el => {
        el.value = el.value.replace(/(\r\n\t|\n|\r\t)/gm, "").trim();
        if ($.inArray(el.name, aOptionName) === -1) {
          aOptionName.push(el.name);
        }
      });

      // Include checkbox not checked
      $('select:not(:checked)').each(function () {
        if ($.inArray(this.name, aOptionName) === -1 && this.name !== '') {
          aOptions.push({
            name: this.name,
            value: ''
          });
        }
      });

      $('input[type="checkbox"]:not(:checked)').each(function () {
        if ($.inArray(this.name, aOptionName) === -1 && this.name !== '') {
          aOptions.push({
            name: this.name,
            value: '0'
          });
        }
      });

      // Filter value setting
      aOptions = mergeObjectRecursive(aOptions, 'name', 'value');

      if (aOptions.length === 0) {
        let toastContent = $('<span>' + pwpData.translate.saveSuccess + '</span>');
        M.toast({
          html: toastContent
        });
        return;
      }

      let aPost = {
        'go': 'saveOptions',
        'callback': 'showModal',
        'data': aOptions
      };

      pwpFunction.runAdminAjax(e, aPost);
    });

    function mergeObjectRecursive(object, keyMatch, keyValue) {
      let newData = [];
      $.map(object, element => {
        if (!$.isEmptyObject(element)) {
          // Get object with double key
          let doubleObject = newData.filter(v => {
            return v[keyMatch] == element[keyMatch];
          });

          if (doubleObject.length) {
            let existingIndex = newData.indexOf(doubleObject[0]);

            // If keyValue as array
            if (Array.isArray(newData[existingIndex][keyValue])) {
              newData[existingIndex][keyValue] = newData[existingIndex][keyValue].concat(element[keyValue]);
            } else {
              let currentValue = newData[existingIndex][keyValue];
              newData[existingIndex][keyValue] = [];
              newData[existingIndex][keyValue].push(currentValue, element[keyValue]);
            }
          } else {
            newData.push(element);
          }
        } else {
          newData = mergeObjectRecursive(element, keyMatch, keyValue);
        }
      });
      return newData;
    }

    function toFormatDate(second) {
      var sec_num = parseInt(second, 10); // don't forget the second param
      var hours = Math.floor(sec_num / 3600);
      var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      var seconds = sec_num - (hours * 3600) - (minutes * 60);

      if (hours < 10) {
        hours = "0" + hours;
      }
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (seconds < 10) {
        seconds = "0" + seconds;
      }
      return hours + 'h ' + minutes + 'm ' + seconds + 's';
    }

    function print_r(o) {
      return JSON.stringify(o, null, '\t').replace(/\n/g, '<br>').replace(/\t/g, '&nbsp;&nbsp;&nbsp;');
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