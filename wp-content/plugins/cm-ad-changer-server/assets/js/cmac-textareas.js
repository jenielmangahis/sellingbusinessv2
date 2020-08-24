(function ($) {

    var CMAC_textareas = {
        /*-----------------------------------------------------------------------------------*/
        /* All the matching text areas
         /*-----------------------------------------------------------------------------------*/

        textareas: {},
        /*-----------------------------------------------------------------------------------*/
        /* tinyMCE settings
         /*-----------------------------------------------------------------------------------*/

        tmc_settings: {},
        /*-----------------------------------------------------------------------------------*/
        /* tinyMCE defaults
         /*-----------------------------------------------------------------------------------*/

        tmc_defaults: {
            theme: 'modern',
            menubar: false,
            wpautop: true,
            indent: false,
            toolbar1: 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen',
            plugins: 'fullscreen,image,wordpress,wpeditimage,wplink',
        },
        /*-----------------------------------------------------------------------------------*/
        /* quicktags settings
         /*-----------------------------------------------------------------------------------*/

        qt_settings: {},
        /*-----------------------------------------------------------------------------------*/
        /* quicktags defaults
         /*-----------------------------------------------------------------------------------*/

        qt_defaults: {
            buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,fullscreen'
        },
        /*-----------------------------------------------------------------------------------*/
        /* Launch TinyMCE-enhanced textareas
         /*-----------------------------------------------------------------------------------*/

        runTinyMCE: function () {

            // get the #content's tinyMCE settings or use default
            var init_settings = typeof tinyMCEPreInit == 'object' && 'mceInit' in tinyMCEPreInit && 'content' in tinyMCEPreInit.mceInit ? tinyMCEPreInit.mceInit.content : CMAC_textareas.tmc_defaults;

            // get the #content's quicktags settings or use default
            CMAC_textareas.qt_settings = typeof tinyMCEPreInit == 'object' && 'qtInit' in tinyMCEPreInit && 'content' in tinyMCEPreInit.qtInit ? tinyMCEPreInit.qtInit.content : CMAC_textareas.qt_defaults;

            var custom_settings = {
//                setup: function (ed) {
//                    ed.on('change', function (e) {
//                        CMAC_textareas.changeName(ed);
//                    });
//                }
            }

            // merge our settings with WordPress' and store for later use
            CMAC_textareas.tmc_settings = $.extend({}, init_settings, custom_settings);

            //all custom text areas, except the one to copy
            CMAC_textareas.textareas = $('div.single_element_wrapper:not(.tocopy) textarea.wp-editor-area');

            //give each a unique ID, TinyMCE will need it later
            CMAC_textareas.textareas.each(function (i) {
                var id = $(this).attr('id');
                if (!id) {
                    id = 'mceEditor-' + (i);
                    $(this).attr('id', id);
                }

                // for some reason in WP I am required to do this in the loop
                // CMAC_textareas.tmc_settings.selector is insufficient, anyone who can tell my why gets a margarita
                var tmc_settings = $.extend({}, CMAC_textareas.tmc_settings, {selector: "#" + id});
                var qt_settings = $.extend({}, CMAC_textareas.qt_settings, {id: id});
                // add our copy to he collection in the tinyMCEPreInit object because switch editors
                // will look there for an wpautop setting specific to this editor
                // similarly quicktags will product a toolbar with no buttons: https://core.trac.wordpress.org/ticket/26183
                if (typeof tinyMCEPreInit == 'object') {
                    tinyMCEPreInit.mceInit[id] = tmc_settings;
                    tinyMCEPreInit.qtInit[id] = qt_settings;
                    //switchEditors.addInstance( id, tmc_settings );
                    //QTags.addInstance( qt_settings );
                }

                // turn on the quicktags editor for each
                quicktags(qt_settings);

                // turn on tinyMCE for each
                tinymce.init(tmc_settings);

                // fix media buttons
                $(this).closest('.customEditor').find('a.insert-media').data('editor', id);

            });  //end each

        }, //end runTinyMCE text areas
        /*-----------------------------------------------------------------------------------*/
        /* Apply TinyMCE to new textareas
         /*-----------------------------------------------------------------------------------*/

        newTinyMCE: function (clone) {

            // count all custom text areas, except the one to copy
            count = CMAC_textareas.textareas.length;

            // assign the new textarea an ID
            id = 'mceEditor-' + count;
            $new_textarea = clone.find('textarea.wp-editor-area').attr('id', id);
            /*
             * insert editor id to new media button
             */
            $($new_textarea[0]).parent().parent().parent().find('.insert-media').attr('data-editor', 'mceEditor-' + count);
            // add new textarea to collection
            CMAC_textareas.textareas.push($new_textarea);

            // Merge new selector into settings
            var tmc_settings = $.extend({}, CMAC_textareas.tmc_settings, {selector: "#" + id});

            var qt_settings = $.extend({}, CMAC_textareas.qt_settings, {id: id});


            // add our copy to he collection in the tinyMCEPreInit object because switch editors
            if (typeof tinyMCEPreInit == 'object') {
                tinyMCEPreInit.mceInit[id] = tmc_settings;
                tinyMCEPreInit.qtInit[id] = qt_settings;
            }

            //QTags.addInstance( qt_settings );

            try{
                // turn on the quicktags editor for each
                quicktags(qt_settings);

                // old way to initialize - doesn't get our new settings
                // tinyMCE.execCommand( 'mceAddEditor', false, id );
                // turn on tinyMCE
                tinyMCE.init(tmc_settings);

            }catch(e){
            }

        }, //end runTinyMCE text areas

        /*-----------------------------------------------------------------------------------*/
        /* Meta Fields Sorting
         /*-----------------------------------------------------------------------------------*/

        sortable: function () {

            var textareaID;
            $('.cmac-textareas').sortable({
                //cancel: ':input,button,.customEditor', // exclude TinyMCE area from the sort handle
                handle: 'h3.handle',
                axis: 'y',
                opacity: 0.5,
                tolerance: 'pointer',
                placeholder: 'sortable-placeholder',
                connectWith: '.connectedSortable',
                dropOnEmpty: true,
                start: function (event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
                    textareaID = $(ui.item).find('textarea.wp-editor-area').attr('id');
                    try{
                        tinyMCE.execCommand('mceRemoveEditor', false, textareaID);
                    }catch(e){
                    }
                },
                stop: function (event, ui) { // re-initialize TinyMCE when sort is completed
                    try{
                        tinyMCE.execCommand('mceAddEditor', false, textareaID);
                    }catch(e){
                    }
                    //			$(this).find('.update-warning').show();
                }
            });

        }, //end of sortable

        /*-----------------------------------------------------------------------------------*/
        /* A Simple Toggle switch
         /*-----------------------------------------------------------------------------------*/

        toggleGroups: function () {

            $('.cmac-textareas').on('click', '.toggle', function () {

                $group = $(this).parents('.single_element_wrapper');
                console.log($group);
                $toggle = $group.find('.toggle_state');
                console.log($toggle);
                $inside = $group.find('.group-inside');
                console.log($inside);

                $inside.toggle('slow', function () {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    $group.find('.group-wrap').toggleClass('closed', $toggle.prop('checked'));
                });

            });

        }, //end toggleGroups

        /*-----------------------------------------------------------------------------------*/
        /* Switch Editors
         /*-----------------------------------------------------------------------------------*/

        switchEditors: function () {

            $('.cmac-textareas').on('click', '.wp-switch-editor', function () {

                $wrapper = $(this).closest('.wp-editor-wrap');
                $wrapper.toggleClass('html-active tmce-active');

                id = $wrapper.find('textarea.wp-editor-area').attr('id');
                mode = $(this).data('mode');

                switchEditors.go(id, mode);

            });

        } //end switchEditors

    }; // End CMAC_textareas Object // Don't remove this, or there's no guacamole for you

    /*-----------------------------------------------------------------------------------*/
    /* Execute the above methods in the CMAC_textareas object.
     /*-----------------------------------------------------------------------------------*/

    $(document).ready(function () {

        CMAC_textareas.runTinyMCE();
        CMAC_textareas.sortable();
        CMAC_textareas.toggleGroups();
        CMAC_textareas.switchEditors();

        //create a div to bind to
        if (!$.wpalchemy) {
            $.wpalchemy = $('<div/>').attr('id', 'wpalchemy').appendTo('body');
        }
        ;

        $('[class*=docopy-]').on('click', function (e)
        {
            e.preventDefault();

            var p = $(this).parents('.cmac-group');

            var the_name = $(this).attr('class').match(/docopy-([a-zA-Z0-9_-]*)/i)[1];
            var the_group = $('.cmac_group-' + the_name + '.tocopy', p).first();

            the_group.find('input[name^="banner_weight"]').spinner("destroy");

            var the_clone = the_group.clone().removeClass('tocopy last');
            var the_props = ['name', 'id', 'for', 'class'];

            the_group.find('*').each(function (i, elem)
            {
                for(var j = 0; j < the_props.length; j++)
                {
                    var the_prop = $(elem).attr(the_props[j]);

                    if (the_prop)
                    {
                        var the_match = the_prop.match(/\[(\d+)\]/i);

                        if (the_match)
                        {
                            the_prop = the_prop.replace(the_match[0], '[' + (+the_match[1] + 1) + ']');

                            $(elem).attr(the_props[j], the_prop);
                        }

                        the_match = null;

                        // todo: this may prove to be too broad of a search
                        the_match = the_prop.match(/n(\d+)/i);

                        if (the_match)
                        {
                            the_prop = the_prop.replace(the_match[0], 'n' + (+the_match[1] + 1));

                            $(elem).attr(the_props[j], the_prop);
                        }
                    }
                }
            });

            the_group.before(the_clone);
            CMAC_textareas.newTinyMCE(the_clone);
            jQuery('input[name^="banner_weight"]').spinner({min: 0, max: 100, step: 10});
            /*
             * suctoim banner js init
             */
            $('.enable_banner_custom_js').on('change', function (e) {
                var simblingTextArea = $(this.nextElementSibling);
                if(this.checked){
                    simblingTextArea.show();
                }else{
                    simblingTextArea.hide();
                    simblingTextArea.val('');
                }
            }).change();
        });

        $('.cmac-group').click(function (e)
        {
            var elem = $(e.target);

            if (elem.attr('class') && elem.filter('[class*=dodelete]').length)
            {
                e.preventDefault();

                if (confirm('This action can not be undone, are you sure?'))
                {
                    elem.parents('.single_element_wrapper').remove();
                }
            }
        });

    });

})(jQuery);