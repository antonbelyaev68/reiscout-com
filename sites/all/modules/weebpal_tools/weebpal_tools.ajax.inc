<?php
include_once drupal_get_path('module', 'weebpal_tools') . '/weebpal_tools.functions.inc';

function weebpal_tools_load_font() {
  $font_family = isset($_POST['font_family']) ? $_POST['font_family'] : NULL;
  $wrapper_id = isset($_POST['wrapper_id']) ? $_POST['wrapper_id'] : NULL;
  if($font_family && $wrapper_id) {
    $font = weebpal_tools_google_fonts_load_font($font_family);
    print json_encode(array(
      'result' => 'success',
      'font' => $font,
      'wrapper_id' => $wrapper_id,
    ));
  }
  else {
    print json_encode(array("result" => "failed", 'wrapper_id' => $wrapper_id));
  }
}
