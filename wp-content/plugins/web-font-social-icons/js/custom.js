//used only on widgets.php
jQuery(document).ready(function ($) {
    $(document.body).on('change', '#widgets-right .icontype', function () {
        var type = $(this).val();
        if(type) {

            var here = $(this).parent().parent();

            var fieldname = here.find('input#fieldname').val();
            fieldname = fieldname.replace("replace", type);

            var service = here.find(".icontype option[value='"+type+"']").text();
            here.find('#socialicons').append('<p><label>'+service+'</label><input type="text" name="'+fieldname+'" placeholder="URL" /></p>');
        }
    });

    $( "#widgets-right #socialicons" ).sortable({
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

    function updateColorPickers(){
        $('#widgets-right .wp-color-picker').each(function(){
            $(this).wpColorPicker();
        });
    }
    updateColorPickers();

    $(document).ajaxSuccess(function(e, xhr, settings) {

        if(settings.data.search('action=save-widget') != -1 ) {
            $( "#widgets-right #socialicons" ).sortable({ items: "> p" });
            $('.color-field .wp-picker-container').remove();
            updateColorPickers();
        }
    });
});

