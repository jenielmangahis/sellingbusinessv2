var cmtad_box_sizes = [
    {"name": "Medium rectangle (300 x 250)", "value": "{\"width\":\"300\",\"height\":\"250\"}"},
    {"name": "Large rectangle (336 x 280)", "value": "{\"width\":\"336\",\"height\":\"280\"}"},
    {"name": "Leaderboard (728 x 90)", "value": "{\"width\":\"728\",\"height\":\"90\"}"},
    {"name": "Wide skyscraper (160x600)", "value": "{\"width\":\"160\",\"height\":\"600\"}"},
    {"name": "Half banner (234x60)", "value": "{\"width\":\"234\",\"height\":\"60\"}"},
    {"name": "Banner (468 x 60)", "value": "{\"width\":\"468\",\"height\":\"60\"}"},
    {"name": "Large leaderboard (970 x 90)", "value": "{\"width\":\"970\",\"height\":\"90\"}"},
    {"name": "Skyscraper (120x600)", "value": "{\"width\":\"120\",\"height\":\"600\"}"},
    {"name": "Vertical banner (120 x 240)", "value": "{\"width\":\"120\",\"height\":\"240\"}"},
    {"name": "Large skyscraper (300 x 600)", "value": "{\"width\":\"300\",\"height\":\"600\"}"},
    {"name": "Square (250 x 250)", "value": "{\"width\":\"250\",\"height\":\"250\"}"},
    {"name": "Small square (200 x 200)", "value": "{\"width\":\"200\",\"height\":\"200\"}"},
    {"name": "Small rectangle (180 x 150)", "value": "{\"width\":\"180\",\"height\":\"150\"}"},
    {"name": "Button (125x125)", "value": "{\"width\":\"125\",\"height\":\"125\"}"},
];

/**
 * Borders
 */
var cmtad_border_styles = [{'name': 'dotted', 'value': 'dotted'},
    {'name': 'solid', 'value': 'solid'},
    {'name': 'dashed', 'value': 'dashed'}];

var cmtad_params = [];

/**
 * Main HTML container
 */
var div;
/**
 * Title container
 */
var title;

/**
 * Description container
 */
var description;

/**
 * Aligns
 */
var cmtad_align = [{'name': 'left', 'value': 'left'},
    {'name': 'center', 'value': 'center'},
    {'name': 'right', 'value': 'right'}];

/**
 * Fonts
 */
var cmtad_fonts = [{'name': 'Helvetica', 'value': 'Helvetica'},
    {'name': 'Verdana', 'value': 'Verdana'},
    {'name': 'Tahoma', 'value': 'Tahoma'},
    {'name': 'Courier New', 'value': 'Courier New'},
    {'name': 'Arial', 'value': 'Arial'},
    {'name': 'Arial Black', 'value': 'Arial Black'},
//        {'name':'Istok Web','value':'Istok Web'},
//        {'name':'Arimo','value':'Arimo'},
//        {'name':'Gudea','value':'Gudea'},
//        {'name':'Exo','value':'Exo'},
//        {'name':'Cousine','value':'Cousine'},
//        {'name':'Open Sans','value':'Open Sans'},
//        {'name':'Open Sans Condensed','value':'Open Sans Condensed'},
//        {'name':'Cuprum','value':'Cuprum'}
];

var addesigner = {
    addParam: function (key, value) {

        cmtad_params[key] = value;
//        console.log(cmtad_params);
        this.generateCSS();
    },
    deleteParam: function (key) {
        cmtad_params[key] = 'delete';
//        console.log(cmtad_params);
        this.generateCSS();
    },
    createSelect: function (element, data, selected) {
        for(var i = 0; i < data.length; i++){
            var option = JSON.parse(JSON.stringify(data[i]));
//            console.log(option);
            element.append(jQuery('<option>', {
                value: option.value,
                text: option.name
            }));
        }
        ;

        if (selected) {
            element.val(selected);
        }

    },
    initCSSContainer: function () {
        div = document.createElement('div');
        div.setAttribute('style', '');
        div.setAttribute('id', 'previewContainerLoadedDiv');

        title = document.createElement('div');
        title.setAttribute('style', '');
        title.setAttribute('id', 'previewContainerLoadedDivTitle');
        title.innerText = "This is a demo banner title.";


        description = document.createElement('div');
        description.setAttribute('style', '');
        description.setAttribute('id', 'previewContainerLoadedDivDescription');
        description.innerText = "This is a demo banner description.";

        div.style['borderStyle'] = 'solid';
        div.style['borderWidth'] = 1;
        div.style['width'] = 250;
        div.style['height'] = 100;

//        console.log('init');
    },
    generateCSS: function () {

        for(var param in cmtad_params){
            if (typeof param != 'undefined') {
//                console.log(param + '-' + cmtad_params[param]);

                if (param == 'titleColor') {
                    title.style['color'] = cmtad_params[param];
                } else if (param == 'titleFontSize') {
                    title.style['fontSize'] = cmtad_params[param];
                } else if (param == 'contentOpacity') {
                    title.style['opacity'] = cmtad_params[param];
                    description.style['opacity'] = cmtad_params[param];
                } else if (param == 'titleTextAlign') {
                    title.style['textAlign'] = cmtad_params[param];
                } else if (param == 'descriptionTextAlign') {
                    description.style['textAlign'] = cmtad_params[param];
                } else {
                    div = this.applyCSS(div, param, cmtad_params[param]);
                }
            }
        }

        div.appendChild(title);
        div.appendChild(description);

        // Create a wrapper to return HTML
        var container = document.createElement('div');
        container.appendChild(div);
        jQuery('#previewContainer').html(container.innerHTML);
        jQuery('#htmlPreviewContainer').html(container.innerHTML);


    },
    applyCSS: function (element, option, value) {
        if (option == 'contentOpacity') {

            // Set content opacity
            //span.style['-ms-filter'] = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + (value*500) + ')';
            element.style['filter'] = "alpha(opacity=" + (value * 100) + ")";
            //span.style['-moz-opacity'] = value;
            //span.style['-khtml-opacity'] = value;
            element.style['opacity'] = value;
            //span.style.filter = value;
            //console.log('alpha(opacity=' + (value*100) + ')');
        } else {
            element.style[option] = value;
            //console.log("Parametr | " + param);
        }

        return element;
    }
};

jQuery(document).ready(function () {

    addesigner.initCSSContainer();

    // Hide all panels bodies
    jQuery('.container .panel-body').hide();

    // #### SELECTS ####
    addesigner.createSelect(jQuery('#cmtad_tooltip_border_style'), cmtad_border_styles, 'solid');
    addesigner.createSelect(jQuery('#cmtad_tooltip_font_family'), cmtad_fonts);
    addesigner.createSelect(jQuery('#cmtad_tooltip_banner_size_predefined'), cmtad_box_sizes);
    addesigner.createSelect(jQuery('#cmtad_tooltip_description_align'), cmtad_align);
    addesigner.createSelect(jQuery('#cmtad_tooltip_title_align'), cmtad_align);

    // #### SLIDERS ####
    var id0 = 'cmtad_tooltip_padding_top';
    jQuery('#' + id0).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id0 + '_amount').text(ui.value + 'px');
            addesigner.addParam('paddingTop', parseFloat(ui.value));
        }
    });

    var id1 = 'cmtad_tooltip_padding_left';
    jQuery('#' + id1).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id1 + '_amount').text(ui.value + 'px');
            addesigner.addParam('paddingLeft', parseFloat(ui.value));
        }
    });

    var id2 = 'cmtad_tooltip_padding_bottom';
    jQuery('#' + id2).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id2 + '_amount').text(ui.value + 'px');
            addesigner.addParam('paddingBottom', parseFloat(ui.value));
        }
    });

    var id3 = 'cmtad_tooltip_padding_right';
    jQuery('#' + id3).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id3 + '_amount').text(ui.value + 'px');
            addesigner.addParam('paddingRight', parseFloat(ui.value));
        }
    });

    var id4 = 'cmtad_tooltip_border_radius';
    jQuery('#' + id4).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id4 + '_amount').text(ui.value + 'px');
            addesigner.addParam('borderRadius', parseFloat(ui.value));
        }
    });

    var id5 = 'cmtad_tooltip_border_width';
    jQuery('#' + id5).slider({
        value: 1, min: 0, max: 12, step: 1,
        change: function (event, ui) {
            jQuery('#' + id5 + '_amount').text(ui.value + 'px');
            addesigner.addParam('borderWidth', parseFloat(ui.value));
        }
    });

    var id6 = 'cmtad_tooltip_opacity';
    jQuery('#' + id6).slider({
        value: 1, min: 0, max: 1, step: 0.1,
        change: function (event, ui) {
            jQuery('#' + id6 + '_amount').text(ui.value + 'px');
            addesigner.addParam('opacity', parseFloat(ui.value));
        }
    });

    var id7 = 'cmtad_tooltip_content_opacity';
    jQuery('#' + id7).slider({
        value: 1, min: 0, max: 1, step: 0.1,
        change: function (event, ui) {
            jQuery('#' + id7 + '_amount').text(ui.value + 'px');
            addesigner.addParam('contentOpacity', parseFloat(ui.value));
        }
    });

    var id8 = 'cmtad_tooltip_font_size';
    jQuery('#' + id8).slider({
        value: 12, min: 6, max: 30, step: 1,
        change: function (event, ui) {
            jQuery('#' + id8 + '_amount').text(ui.value + 'px');
            addesigner.addParam('fontSize', parseFloat(ui.value));
        }
    });

    var id9 = 'cmtad_tooltip_title_size';
    jQuery('#' + id9).slider({
        value: 12, min: 6, max: 30, step: 1,
        change: function (event, ui) {
            jQuery('#' + id9 + '_amount').text(ui.value + 'px');
            addesigner.addParam('titleFontSize', parseFloat(ui.value));
        }
    });

    var id10 = 'cmtad_tooltip_banner_width';
    jQuery('#' + id10).slider({
        value: 250, min: 0, max: 1000, step: 1,
        slide: function (event, ui) {
            jQuery('#' + id10 + '_amount').text(ui.value + 'px');
            addesigner.addParam('width', parseFloat(ui.value));
        },
        change: function (event, ui) {
            jQuery('#' + id10 + '_amount').text(ui.value + 'px');
            addesigner.addParam('width', parseFloat(ui.value));
        }
    });

    var id11 = 'cmtad_tooltip_banner_height';
    jQuery('#' + id11).slider({
        value: 100, min: 0, max: 1000, step: 1,
        slide: function (event, ui) {
            jQuery('#' + id11 + '_amount').text(ui.value + 'px');
            addesigner.addParam('height', parseFloat(ui.value));
        },
        change: function (event, ui) {
            jQuery('#' + id11 + '_amount').text(ui.value + 'px');
            addesigner.addParam('height', parseFloat(ui.value));
        }
    });

    // #### COLOR PICKERS ####
    var cid1 = 'cmtad_tooltip_background_color';
    $('#' + cid1).colorpicker().on('changeColor', function (ev) {
        jQuery('#' + cid1 + '_input').css('color', '#fff');
        jQuery('#' + cid1 + '_input').css('backgroundColor', ev.color.toHex());
        addesigner.addParam('backgroundColor', ev.color.toHex());

    });
    var cid2 = 'cmtad_tooltip_border_color';
    $('#' + cid2).colorpicker().on('changeColor', function (ev) {
        jQuery('#' + cid2 + '_input').css('color', '#fff');
        jQuery('#' + cid2 + '_input').css('backgroundColor', ev.color.toHex());
        addesigner.addParam('borderColor', ev.color.toHex());

    });
    var cid3 = 'cmtad_tooltip_font_color';
    $('#' + cid3).colorpicker().on('changeColor', function (ev) {
        jQuery('#' + cid3 + '_input').css('color', '#fff');
        jQuery('#' + cid3 + '_input').css('backgroundColor', ev.color.toHex());
        addesigner.addParam('color', ev.color.toHex());

    });
    var cid4 = 'cmtad_tooltip_title_color';
    $('#' + cid4).colorpicker().on('changeColor', function (ev) {
        jQuery('#' + cid4 + '_input').css('color', '#fff');
        jQuery('#' + cid4 + '_input').css('backgroundColor', ev.color.toHex());
        addesigner.addParam('titleColor', ev.color.toHex());

    });

    // #### OTHERS ####

    jQuery('#panel_banner_size,#panel_paddings,#panel_bg,#panel_border,#panel_description,#panel_font,#panel_title').on('click', function () {
        var id = jQuery(this).attr('id');
        jQuery('#' + id + '_body').toggle();
        return false;
    });

    jQuery('#cmtad_tooltip_title_text').on('keyup', function () {
        jQuery('#previewContainerLoadedDivTitle').text(jQuery(this).val());
        jQuery('#htmlPreviewContainer').html(jQuery('#previewContainer').html());
    });

    jQuery('#cmtad_tooltip_description_text').on('keyup', function () {
        jQuery('#previewContainerLoadedDivDescription').text(jQuery(this).val());
        jQuery('#htmlPreviewContainer').html(jQuery('#previewContainer').html());
    });

    // #### CHANGE ####
    jQuery('#cmtad_tooltip_banner_size_predefined').on('change', function () {
        var data = JSON.parse(jQuery(this).val());

        jQuery("#cmtad_tooltip_banner_width").slider('option', 'value', parseFloat(data.width));
        jQuery("#cmtad_tooltip_banner_height").slider('option', 'value', parseFloat(data.height));
    });

    jQuery('#cmtad_tooltip_border_style').on('change', function () {
        addesigner.addParam('borderStyle', this.value);
    });

    jQuery('#cmtad_tooltip_border_radius').on('change', function () {
        addesigner.addParam('borderRadius', this.value);
    });

    jQuery('#cmtad_tooltip_description_align').on('change', function () {
        addesigner.addParam('descriptionTextAlign', this.value);
    });

    jQuery('#cmtad_tooltip_title_align').on('change', function () {
        addesigner.addParam('titleTextAlign', this.value);
    });

    jQuery('#cmtad_tooltip_font_family').on('change', function () {
        addesigner.addParam('fontFamily', this.value);
    });

    // #### CLICK ####
    jQuery('#htmlPreviewContainer').on('click', function () {
        this.select();
        return false;
    });

    jQuery('.adcheckbox').on('click', function () {
//		if (!this.checked) {
//			var rel = jQuery(this).parent('.row').find('div').attr('rel');
//			if (typeof rel != 'undefined') {
//				addesigner.deleteParam(rel);
//				console.log(this.checked + ':' + rel);
//			}
//		}
    });
});