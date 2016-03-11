(function ($) {

Drupal.WeebPalToolsGWF = Drupal.WeebPalToolsGWF || {};

Drupal.behaviors.actionWeebPalToolsGWF = {
  attach: function (context) {
    Drupal.WeebPalToolsGWF.initGWF();
    $('select.font-family').change(function() {
      var font_family = $(this).val();
      var wrapper_id = $(this).closest('fieldset').attr('id');
      $.ajax({
        type: "POST",
        url: Drupal.settings.basePath + Drupal.settings.ajax_link + "admin/structure/weebpal-tools/load-font",
        data: { 'font_family': font_family, 'wrapper_id': wrapper_id},
        complete: function( msg ) {
          var data = $.parseJSON(msg.responseText);
          Drupal.WeebPalToolsGWF.updateForm(data);
        }
      });        
    });
  }
};

Drupal.WeebPalToolsGWF.updateForm = function(data, values) {
  var result = data.result;
  var font = data.font;
  var wrapper_id = data.wrapper_id;
  var wrapper = $('#' + wrapper_id);

  var category = wrapper.find('#' + wrapper_id + "-category");
  category.val(values ? values['category'] : "");
  var variant = wrapper.find('#' + wrapper_id + "-variant");
  variant.children().remove();
  var subset = wrapper.find('#' + wrapper_id + "-subset");
  subset.children().remove();
  var version = wrapper.find('#' + wrapper_id + "-version");
  version.val(values ? values['version'] : "");
  var file = wrapper.find('#' + wrapper_id + "-file");
  file.children().remove();
  if(result === 'success') {
    if(font) {
      category.val(font.category);
      version.val(font.version);
      for(x in font.variants) {
        var option = $('<option value="' + font.variants[x] + '"' + (values && font.variants[x] === values['variant'] ? 'selected = "selected"' : '') + '>' + font.variants[x] + "</option>");
        variant.append(option);
      }
      for(x in font.subsets) {
        var option = $('<option value="' + font.subsets[x] + '"' + (values && font.subsets[x] === values['subset'] ? 'selected = "selected"' : '') + '>' + font.subsets[x] + "</option>");
        subset.append(option);
      }
      for(x in font.files) {
        var option = $('<option value="' + x + '"' + (values && x === values['files'] ? 'selected = "selected"' : '') + '>' + font.files[x] + "</option>");
        file.append(option);
      }  
    }
  }
};

Drupal.WeebPalToolsGWF.initGWF = function() {
  $('select.font-family').each(function() {
    var font_family = $(this).val();
    var wrapper_id = $(this).closest('fieldset').attr('id');
    var values = {};
    values['category'] = $("#" + wrapper_id + "-category").val();
    values['version'] = $("#" + wrapper_id + "-version").val();
    values['variant'] = $("#" + wrapper_id + "-variant").val();
    values['subset'] = $("#" + wrapper_id + "-subset").val();
    values['file'] = $("#" + wrapper_id + "-file").val();
    $.ajax({
      type: "POST",
      url: Drupal.settings.basePath + Drupal.settings.ajax_link + "admin/structure/weebpal-tools/load-font",
      data: { 'font_family': font_family, 'wrapper_id': wrapper_id},
      complete: function( msg ) {
        var data = $.parseJSON(msg.responseText);
        Drupal.WeebPalToolsGWF.updateForm(data, values);
      }
    });        
  });
};    

})(jQuery);
