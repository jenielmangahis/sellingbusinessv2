// used in shortcode popup

jQuery(document).ready(function ($) {

    $(document.body).on('change', '#purethemes-popup-form .icontype', function () {
        var type = $(this).val();
        if(type) {
            var here = $(this).parent().parent();
            var service = here.find(".icontype option[value='"+type+"']").text();
            here.find('#socialicons').append('<p><label>'+service+'</label><input type="text" name="'+type+'" placeholder="URL" /></p>');
        }
        if( $('#purethemes-popup-form .icontype').hasClass('notloaded')) {
            wfsi.load();
            wfsi.sort();

            $('#purethemes-popup-form .icontype').removeClass('notloaded');
        }
    });

    var wfsi = {
        sort: function() {
            $("#purethemes-popup-form #socialicons" ).sortable({
                items: "> p",
                placeholder: "ui-state-highlight",
                revert:300,
                over: function(e, ui) { sortableIn = 1; },
                out: function(e, ui) { sortableIn = 0; },
                beforeStop: function (event, ui) {
                    newItem = ui.item;
                    if (sortableIn == 0) {
                      ui.item.remove();
                  }
              },
            });
        },
        gensc: function () { // function to build shortcode based on fields
            var output,
            counticons = $('#purethemes-popup-form #socialicons p').length,
            iconsize = $('#purethemes-popup-form #iconsize').val(),
            target = $('#purethemes-popup-form #target').val();

            if(counticons > 1) {
                var output = '[pt_social_icons]';
                $('#purethemes-popup-form #socialicons p').each(function () {
                    //[social_icon service="twitter" url="#"]
                    var name = $(this).find('input').attr('name'),
                    val = $(this).find('input').val();
                    output += "[pt_social_icon target=\"" + target + "\" iconsize=\"" + iconsize + "\" service=\"" + name + "\" url=\"" + val + "\" ]";
                });
                output += "[/pt_social_icons]";
            } else {
                var name = $('#purethemes-popup-form #socialicons p input').attr('name'),
                val = $('#purethemes-popup-form #socialicons p input').val();
                output = "[pt_social_icon target=\"" + target + "\" iconsize=\"" + iconsize + "\"  type=\"single\" service=\"" + name + "\" url=\"" + val + "\" ]";
            }

            return output;
        },

        load: function () {
            popup = $('#purethemes-popup'),
            form = $('#form-container-ajax', popup);
            $('.ptsc-insert', form).click(function () {
                if (window.tinyMCE) {
                    var out = "";
                    var out = wfsi.gensc();
                    tinymce.activeEditor.insertContent(out);
                    var out = "";
                    tb_remove();

                }
            });

            // resize TB
            wfsi.resizeTB();
            $(window).resize(function () {
                wfsi.resizeTB();
            });

        },
        resizeTB: function () {

            var tbAjax = $('#TB_ajaxContent'),
            tbWindow = $('#TB_window'),
            ptsc_popup = $('#purethemes-popup');

            ptsc_popup.css({
                maxHeight: $(window).height()*0.6
            })

            tbWindow.css({
                height: ptsc_popup.outerHeight() + 50,
                //width: ptsc_popup.outerWidth(),
                marginLeft: -(ptsc_popup.outerWidth() / 2)
            });

            tbAjax.css({
                paddingTop: 0,
                paddingLeft: 0,
                paddingRight: 0,
                height: (tbWindow.outerHeight() - 47),
                overflow: 'auto', // IMPORTANT
                //width: tbWindow.outerWidth()-2
            });
        }
    }
    wfsi.load();


});