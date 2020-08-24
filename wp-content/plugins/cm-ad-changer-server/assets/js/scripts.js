var CM_AdsChanger = {}, plugin_url;
CM_AdsChanger.BannerVariations = new Array();
plugin_url = window.cmac_data.pluginurl;

(function ($) {

    CM_AdsChanger.delete_all_banners = function () {
        if (!confirm("Are you sure?\nThis will delete all images and their variations!")) {
            return;
        }
        $('div#filelist').find('.delete_button').trigger('click');
    };

    CM_AdsChanger.delete_banner = function (obj) {
        if (obj.prevAll('img').attr('plupload_id')) {
            plupload_id = obj.prevAll('img').attr('plupload_id');
            CM_AdsChanger.uploader.removeFile(plupload_id);
        }

        obj.parent().fadeOut('slow', function () {
            if ($(this).hasClass('selected')) {
                $('#selected_banner').html('');
                $('#selected_banner_url').html('');
                $('.selected_banner_details').hide();
                $('.selected_banner_details input[type="hidden"]').val('');
            }
            $(this).remove();
        });
    };

    CM_AdsChanger.check_banner = function (obj) {
        obj.siblings().removeClass('selected');
        obj.addClass('selected');
        $('#selected_banner_url').html(obj.find('img.banner_image').attr('src').replace('tmp/', ''));
        if (obj.find('input[type="text"]').val() !== '')
            $('#selected_banner').html(obj.find('input[type="text"]').val());
        else
            $('#selected_banner').html('Untitled');
        $('.selected_banner_details input[type="hidden"]').val(obj.find('input[type="hidden"]').val());
        $('.selected_banner_details').show();
    };

    CM_AdsChanger.select_ad = function (obj) {
        var selectedId;

        obj.siblings().removeClass('selected');

        if (CM_AdsChanger.check_display_method(obj))
        {
            obj.addClass('selected');
            selectedId = $(obj).find('input[type="hidden"][name="banner_ids[]"]').val();
            $('[name^="selected"]').val(selectedId);
        }
    };

    CM_AdsChanger.check_display_method = function (obj) {
        if (obj.length)
        {
            var selectedIdIsChecked = false;
            $(obj).parents('.campaign_type_part').find('#use_selected_banner')[0].checked;
            $(obj).parents('.campaign_type_part').find('#use_selected_banner').each(function(select){
                if(!this.disabled && this.checked){
                    selectedIdIsChecked = true;
                }
            });

            if (!selectedIdIsChecked)
            {
                $('.single_element_wrapper').removeClass('selected');
                $('[name^="selected"]').val(null).attr('disabled', true);
            }
            else
            {
                $('[name^="selected"]').val(null).attr('disabled', false);
            }
            return selectedIdIsChecked;
        }
    };

    CM_AdsChanger.set_variation_plupload = function (obj) {
        var obj = obj;
        next_banner_index++;
        CM_AdsChanger.BannerVariations[next_banner_index] = {};
        CM_AdsChanger.BannerVariations[next_banner_index].id = obj.find('.pickfiles').attr('id');
        CM_AdsChanger.BannerVariations[next_banner_index].uploader = new plupload.Uploader({
            runtimes: 'gears,html5,flash,silverlight,browserplus',
            browse_button: CM_AdsChanger.BannerVariations[next_banner_index].id,
            container: 'banner_variations_container',
            max_file_size: '2mb',
            url: ajaxurl + '?action=ac_upload_image&pic_type=banner_variation',
            flash_swf_url: plugin_url + 'assets/js/plupload/plupload.flash.swf',
            silverlight_xap_url: plugin_url + 'assets/js/plupload/plupload.silverlight.xap',
            filters: [
                {title: "Image files", extensions: "jpg,jpeg,gif,png"}
            ]//,
//			resize : {width : 320, height : 240, quality : 90}
        });

        CM_AdsChanger.BannerVariations[next_banner_index].uploader.init();
        CM_AdsChanger.BannerVariations[next_banner_index].uploader.bind('FilesAdded', function (up, files) {
            up.refresh(); // Reposition Flash/Silverlight
            this.start();
        });

        CM_AdsChanger.BannerVariations[next_banner_index].uploader.bind('BeforeUpload', function (up, file) {
            if (obj.find('.banner_variation').length >= banner_variations_limit) {
                alert('Variations limit achieved: ' + banner_variations_limit);
                up.stop();
            }
        });

        CM_AdsChanger.BannerVariations[next_banner_index].uploader.bind('FileUploaded', function (up, file, response) {
            var pattern = /^Error:/;
            if (!pattern.test(response.response)) {
                response = ($.parseJSON(response.response));
                html = '<div class="banner_variation">';
                html += '<img src="' + upload_tmp_path + response.thumb_filename + '" class="banner_variation_image" plupload_id="' + file.id + '" />';
                html += '<input type="hidden" name="banner_variation[' + obj.find('input[name^="banner_filename"]').val() + '][]" value="' + response.image_filename + '" />';
                html += '<div class="variation_dimensions">' + response.info.width + 'x' + response.info.height + '</div>';
                html += '<img src="' + plugin_url + '/assets/images/close.png' + '" class="delete_button" />';
                html += '</div>';
                obj.find('.banner_variations').append(html);
                $('.banner_variation').last().find('.delete_button').eq(0).bind('click', function () {
                    CM_AdsChanger.delete_banner($(this));
                });
            } else
                alert('Error');
        });
    };

    CM_AdsChanger.show_add_advertiser_fields = function () {
        $('#add_advertiser_fields').remove();
        $('#manage_advertiser_fields').remove();
        html = '<div id="add_advertiser_fields">';
        html += '<input type="text" id="advertiser_name" name="advertiser_name" value="" />';
        html += '<a href="javascript:void(0)" id="add_advertiser_link" class="advertiser_link" >Add</a>';
        html += '</div>';
        jQuery('#advertisers #advertiser_id').after(html);
    };

    CM_AdsChanger.show_advertiser_namagement_fields = function () {
        $('#add_advertiser_fields').remove();
        $('#manage_advertiser_fields').remove();
        html = '<div id="manage_advertiser_fields">';
        html += '<input type="text" id="advertiser_name" name="advertiser_name" value="' + $('select#advertiser_id option[value="' + $("select#advertiser_id").val() + '"]').html() + '"/>';
        html += '<a href="javascript:void(0)" id="edit_advertiser_link" class="advertiser_link" >Save</a>&nbsp;';
        html += '<a href="javascript:void(0)" id="delete_advertiser_link" class="advertiser_link" >Delete</a>&nbsp;';
        html += '<a href="javascript:void(0)" id="reset_advertiser_link" class="advertiser_link" >Cancel</a>';
        html += '</div>';
        $('#advertisers #advertiser_id').after(html);
    };
    // functions: end

    $(document).ready(function () {

        $('#new_campaign_button').click(function () {
            document.location.href = base_url + '/wp-admin/admin.php?page=ac_server_campaigns&acs_admin_action=new_campaign';
        });

        $('#new_group_button').click(function () {
            document.location.href = base_url + '/wp-admin/admin.php?page=ac_server_groups&acs_admin_action=new_group';
        });
        function viewErrorsDiv(error){
            var errordiv = jQuery('#errors_container');
            var errorContent = jQuery('#errors_container_li');
            if(errordiv){
                errorContent.html(error);
                errordiv.fadeIn( 400 ).delay( 1800 ).fadeOut( "slow" );
            }
        }
        $('#add_campaign_button').click(function (e) {
            e.preventDefault();
            var group_id, campaign_id, group_name, group_order;
            group_id = jQuery('input[name="group_id"]').val();
            var nonce = jQuery('input[name="groups_settings_noncename"]').val();
            campaign_id = jQuery('select[name="new_campaign_id"]').val();
            if(typeof group_id == 'undefined'){
                group_name = jQuery('input[name="description"]').val();
                if(group_name == ''){
                    viewErrorsDiv('Please fill group name.');
                    return false;
                }
                group_order = jQuery('input[name="group_order"]:checked').val();
                if(typeof group_order == 'undefined'){
                    viewErrorsDiv('Please choose group order.');
                    return false;
                }
                document.location.href = base_url + '/wp-admin/admin.php?page=ac_server_groups&group_id=' + group_id + '&action=add_campaign&campaign_id=' + campaign_id + '&group_name=' + group_name + '&group_order=' + group_order + '&groups_settings_noncename=' + nonce;
            }else{
                document.location.href = base_url + '/wp-admin/admin.php?page=ac_server_groups&group_id=' + group_id + '&action=add_campaign&campaign_id=' + campaign_id + '&groups_settings_noncename=' + nonce;
            }
        });
        // uploader start

        if (typeof plupload !== 'undefined')
        {

            CM_AdsChanger.uploader = new plupload.Uploader({
                runtimes: 'gears,html5,flash,silverlight,browserplus',
                browse_button: 'pickfiles',
                container: 'container',
                max_file_size: '10mb',
                url: ajaxurl + '?action=ac_upload_image&pic_type=banner',
                flash_swf_url: plugin_url + 'assets/js/plupload/plupload.flash.swf',
                silverlight_xap_url: plugin_url + 'assets/js/plupload/plupload.silverlight.xap',
                filters: [
                    {title: "Image files", extensions: "jpg,jpeg,gif,png"}
                ]//,
//			resize : {width : 320, height : 240, quality : 90}
            });

            if (jQuery('#container').length)
            {
                CM_AdsChanger.uploader.init();
            }

            CM_AdsChanger.uploader.bind('FilesAdded', function (up, files) {
                up.refresh(); // Reposition Flash/Silverlight
                CM_AdsChanger.uploader.start();
            });
            CM_AdsChanger.uploader.bind('BeforeUpload', function (up, file) {
                if ($('.plupload_image').length >= banners_limit) {
                    alert('Banners limit achieved: ' + banners_limit);
                    up.stop();
                }
            });
            CM_AdsChanger.uploader.bind('FileUploaded', function (up, file, response) {
                var next_banner_index, filename, filename_parts, filename_without_ext, banner_title, html;
                var pattern = /^Error:/;
                if (!pattern.test(response.response))
                {
                    response = ($.parseJSON(response.response));
                    // incrementing next banner index to generate input id attribute (for label funcionate)
                    next_banner_index = $('.plupload_image').length + 1;
                    // getting default name field value
                    filename = file.name;
                    filename_parts = filename.split('.');
                    filename_without_ext = '';
                    for(i = 0; i < filename_parts.length - 1; i++)
                        filename_without_ext += filename_parts[i];
                    if (filename_without_ext.length > 20)
                        banner_title = filename_without_ext.substr(0, 19);
                    else
                        banner_title = filename_without_ext;
                    html = '<div class="plupload_image">';
                    html += '<img src="' + upload_tmp_path + response.thumb_filename + '" class="banner_image" plupload_id="' + file.id + '" />';
                    html += '<div class="ac_explanation clear" style="float:left">Click on image to select the banner</div>';
                    html += '<input type="hidden" name="banner_filename[]" value="' + response.image_filename + '" />';
                    html += '<table class="banner_info">';
                    html += '<tr>';
                    html += '<td><label for="new_banner_title' + next_banner_index + '">Name</label><div class="field_help" title="' + label_descriptions.banner_title + '"></div></td>';
                    html += '<td><input type="text" name="banner_title[]" id="new_banner_title' + next_banner_index + '" maxlength="50" value="' + banner_title + '" /></td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<td><label for="new_banner_title_tag' + next_banner_index + '">Banner Title</label><div class="field_help" title="' + label_descriptions.banner_title_tag + '"></div></td>';
                    html += '<td><input type="text" name="banner_title_tag[]" id="new_banner_title_tag' + next_banner_index + '" maxlength="50" /></td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<td><label for="new_banner_alt_tag' + next_banner_index + '">Banner Alt</label><div class="field_help" title="' + label_descriptions.banner_alt_tag + '"></div></td>';
                    html += '<td><input type="text" name="banner_alt_tag[]" id="new_banner_alt_tag' + next_banner_index + '" maxlength="50" /></td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<td><label for="new_banner_link' + next_banner_index + '">Target URL</label><div class="field_help" title="' + label_descriptions.banner_link + '"></div></td>';
                    html += '<td><input type="text" name="banner_link[]" id="new_banner_link' + next_banner_index + '" maxlength="150" /></td>';
                    html += '</tr>';
                    html += '<tr>';
                    html += '<td><label for="new_banner_weight' + next_banner_index + '">Weight</label><div class="field_help" title="' + label_descriptions.banner_weight + '"></div></td>';
                    html += '<td><input type="text" name="banner_weight[]" id="new_banner_weight' + next_banner_index + '" maxlength="20" class="num_field" value="0" /></td>';
                    html += '</tr>';
                    html += '</table>';
                    html += '<div class="clicks_and_impressions">';
                    html += '<div class="impressions">0</div>';
                    html += '<div class="clicks">0</div>';
                    html += '<div class="percent">0</div>';
                    html += '</div>';
                    html += '<img src="' + plugin_url + '/assets/images/close.png' + '" class="delete_button" />';
                    html += '<div class="clear"></div><div class="banner_variations" id="banner_variations_container">';
                    html += '<input type="button" value="Add variations" id="banner_variation' + next_banner_index + '" class="pickfiles clear"><div class="clear"></div>';
                    html += '</div>';
                    html += '</div>';

                    $('#filelist').prepend(html);

                    $('input#new_banner_weight' + next_banner_index).spinner({min: 0, max: 100, step: 10});

                    $('.delete_button').eq(0).bind('click', function () {
                        CM_AdsChanger.delete_banner($(this));
                    });

                    $('.plupload_image img.banner_image').eq(0).bind('click', function () {
                        CM_AdsChanger.check_banner($(this).parent());
                    });

                    $('.plupload_image').eq(0).find('.field_help').tooltip({
                        show: {
                            effect: "slideDown",
                            delay: 100
                        },
                        position: {
                            my: "left top",
                            at: "right top"
                        }
                    });

                    CM_AdsChanger.set_variation_plupload($('.plupload_image').eq(0));
                } else
                    alert(response.response);
            });
        }

        $('.delete_button').click(function () {
            CM_AdsChanger.delete_banner($(this));
        });

        $('#container').on('click', '#remove_all_images', function () {
            CM_AdsChanger.delete_all_banners();
        });

        $('.plupload_image img.banner_image').click(function () {
            CM_AdsChanger.check_banner($(this).parent());
        });

        $('.cmac-group').on('click', '.single_element_wrapper', function () {
            CM_AdsChanger.select_ad($(this));
        });

        $('.campaign-type-part input[name="banner_display_method"]').on('change', function () {
            CM_AdsChanger.check_display_method(this);
        }).trigger('change');

        // uploader end

        // categories start

        $('#add_category').click(function (e) {
            e.preventDefault();
            if ($('.categories input[type="checkbox"]').length >= 10)
                return;
            if ($('.categories .category_row').length === 0) {
                $('.categories').empty();
            }

            $('.categories').append('<div class="category_row"><!--<input type="checkbox" aria-required="true" name="categories[]" value="" />&nbsp;' + "\n" + '--><input type="text" name="category_title[]" />' + "\n" + '<!--<input type="hidden" name="category_ids[]" value="" />' + "\n" + '--><a href="#" class="delete_link"><img src="' + plugin_url + '/assets/images/close.png' + '" /></a></div>');
            $('.categories .delete_link').eq(-1).bind('click', function (e) {
                e.preventDefault();
                $(this).parent().remove();
                if ($('.categories .category_row').length === 0)
                {
                    $('.categories').html('There are no domain limitations set');
                }
            });
        });
        $('.categories .delete_link').click(function (e) {
            e.preventDefault();
            if (!confirm("Are you sure?\nThis will delete the category and it's relations to other campaigns"))
                return;
            $(this).parent().remove();
            if ($('.categories .category_row').length === 0)
            {
                $('.categories').html('There are no domain limitations set');
            }
        });
        $('#check_all_cats_link').click(function () {
            $('.categories').find('input[type="checkbox"]').attr('checked', 'checked');
        });
        $('#uncheck_all_cats_link').click(function () {
            $('.categories').find('input[type="checkbox"]').removeAttr('checked');
        });
        // categories end

        // dates
        $('#add_active_date_range').click(function (e) {
            var html;
            e.preventDefault();
            if ($('#dates .date_range_row').length >= 10)
                return;
            if ($('#dates .date_range_row').length === 0)
                $('#dates').empty();
            html = '<div class="date_range_row">';
            html += '<input type="text" name="date_from[]" class="date" />&nbsp;';
            html += '<input class="h_spinner ac_spinner" name="hours_from[]" value="0" />&nbsp;h&nbsp;';
            html += '<input class="m_spinner ac_spinner" name="mins_from[]" value="0" />&nbsp;m';
            html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' + plugin_url + '/assets/images/arrow_right.png' + '" style="vertical-align:bottom" />&nbsp;&nbsp;&nbsp;&nbsp;';
            html += '<input type="text" name="date_till[]" class="date" />&nbsp;';
            html += '<input class="h_spinner ac_spinner" name="hours_to[]" value="0" />&nbsp;h&nbsp;';
            html += '<input class="m_spinner ac_spinner" name="mins_to[]" value="0" />&nbsp;m&nbsp;';
            html += '<a href="#" class="delete_link"><img src="' + plugin_url + '/assets/images/close.png' + '" /></a>';
            html += '</div>';
            $('#dates').append(html);
            $('#dates .date_range_row').eq(-1).find('input[type="text"]').datepicker();
            $('.date_range_row').eq(-1).find('.h_spinner').spinner({
                max: 24,
                min: 0
            });
            $('.date_range_row').eq(-1).find('.m_spinner').spinner({
                max: 50,
                min: 0,
                step: 10
            });
            $('#dates .delete_link').eq(-1).bind('click', function (e) {
                e.preventDefault();
                $(this).parent().remove();
                if ($('#dates .date_range_row').length === 0)
                    $('#dates').html('There are no date limitations set');
            });
        });
        $('#dates .date_range_row input[type="text"]').datepicker();
        $('#banner_dates input[type="text"]').datepicker();
        $('.date_range_row .h_spinner').spinner({
            max: 24,
            min: 0
        });
        $('.date_range_row .m_spinner').spinner({
            max: 50,
            min: 0,
            step: 10
        });
        $('#dates .delete_link').click(function (e) {
            e.preventDefault();
            $(this).parent().remove();
            if ($('#dates .date_range_row').length === 0)
                $('#dates').html('There are no date limitations set');
        });
        $('.delete_campaign_link').click(function (e) {
            if (!confirm('Are you sure?')) {
                e.preventDefault();
                return false;
            }
        });
        $('#acs_div_wrapper').click(function () {
            if ($(this).attr('checked') == 'checked')
                $('#class_name_fields').css('display', 'inline-block');
            else
                $('#class_name_fields').hide();
        });
        $('#advertisers').delegate('.advertiser_link', 'click', function () {
            data = {};
            advertiser_id = -1;
            var link_id = $(this).attr('id');
            switch(link_id){
                case 'add_advertiser_link':
                    if ($('#advertiser_name').val() == '') {
                        alert('Please type advertiser name');
                        return false;
                    }
                    data.action = 'ac_add_advertiser';
                    data.advertiser_name = $('#advertisers #advertiser_name').val();
                    break;
                case 'edit_advertiser_link':
                    if ($('#advertiser_name').val() == '') {
                        alert('Please type advertiser name');
                        return false;
                    }
                    data.action = 'ac_edit_advertiser';
                    data.advertiser_name = $('#advertisers #advertiser_name').val();
                    data.advertiser_id = $('#advertisers #advertiser_id').val();
                    break;
                case 'delete_advertiser_link':
                    data.action = 'ac_delete_advertiser';
                    data.advertiser_id = $('#advertisers #advertiser_id').val();
                    break;
                case 'reset_advertiser_link':
                    CM_AdsChanger.show_add_advertiser_fields();
                    return;
                default:
                    return false;
            }


            $.ajax({
                'url': ajaxurl,
                'type': 'post',
                'data': data
            }).done(function (response) {
                response = ($.parseJSON(response));
                if (response.success) {
                    switch(link_id){
                        case 'add_advertiser_link':
                            $('#advertiser_id').append('<option value="' + response.advertiser_id + '">' + $('#advertiser_name').val() + '</option>');
                            $('#advertiser_id').find('option').removeAttr('selected');
                            $('#advertiser_id').find('option').last().attr('selected', 'selected');
                            CM_AdsChanger.show_advertiser_namagement_fields();
                            break;
                        case 'edit_advertiser_link':
                            $('select#advertiser_id option[value="' + $('#advertiser_id').val() + '"]').html($('#advertiser_name').val());
                            break;
                        case 'delete_advertiser_link':
                            $('select#advertiser_id option[value="' + $('#advertiser_id').val() + '"]').remove();
                            CM_AdsChanger.show_add_advertiser_fields();
                            break;
                    }

                    alert(response.success);
                } else
                    alert(response.error)
            })
        })

        $('select#advertiser_id').change(function () {
            if ($(this).val() != '0') {
                CM_AdsChanger.show_advertiser_namagement_fields();
            }
            else {
                CM_AdsChanger.show_add_advertiser_fields();
            }
        })

        $('input#cloud_url').on("keyup", function (e) {
            if ($(this).val() == '') {
                if ($('input#use_cloud[checked="checked"]').length > 0)
                    $('input#use_cloud').removeAttr('checked');
            } else {
                if ($('input#use_cloud[checked="checked"]').length == 0)
                    $('input#use_cloud').attr('checked', 'checked');
            }
        })

        $('.field_help').tooltip({
            show: {
                effect: "slideDown",
                delay: 100
            },
            position: {
                my: "left top",
                at: "right top"
            },
            content: function () {
                var element = $(this);
                return element.attr('title');
            }
        });
        $('.field_tip').tooltip({
            show: {
                effect: "slideDown",
                delay: 100
            }
        });
        $('#ac-fields').tabs();

        if ($('.plupload_image').length > 0) {
            for(i = 0; i < $('.plupload_image').length; i++){
                CM_AdsChanger.set_variation_plupload($('.plupload_image').eq(i));
            }
        }
    });

    $(document).ready(function () {
        $('#campaign_type_id').on('change', function (e) {
            var $this = $(this), $campaignTypes = $('.campaign_type_part');
            $campaignTypes.hide();
            $campaignTypes.find(':input').attr('disabled', 'disabled');
            $($campaignTypes[$this.val()]).show().find(':input').attr('disabled', false);
        }).change();

        $('.adddesigner_trigger').on('click', function () {

            jQuery('#cmac_addesigner_container').dialog({
                height: 600,
                width: 800,
                position: { my: "right center", at: "right center", of: window },
                modal: false,
                closeText: "Hide CM AdDesigner"
            });

            return false;
        });
        $('#user_show_method').on('change', function (e) {
            var resetField = $('#resetFloatingBannerCookieContainer');
            if(this.value == 'once'){
                resetField.show();
            }else{
                resetField.hide();
            }
        }).change();
        $('#user_show_method-flying-bottom').on('change', function (e) {
            var resetField = $('#resetFloatingBottomBannerCookieContainer');
            if(this.value == 'once'){
                resetField.show();
            }else{
                resetField.hide();
            }
        }).change();

        $('textarea[name="html_ads[]"]').each(function(i){
            var $textarea = $(this);
            $(this).on('blur', function(){
                var content = $textarea.val();
                tinymce.get(id).setContent(content);
            });
        });
        $('.enable_banner_custom_js').on('change', function (e) {
            var simblingTextArea = $(this.nextElementSibling);
            if(this.checked){
                simblingTextArea.show();
            }else{
                simblingTextArea.hide();
                /*
                 * Clear the custom js code
                 */
                simblingTextArea.val('');
            }
        }).change();
    });
})(jQuery);
