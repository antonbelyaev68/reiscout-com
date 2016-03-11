<?php
function weebpal_tools_google_fonts_available_fonts($reset = FALSE) {
  $fontsbuffer = variable_get('weebpal_tools_google_fonts_webfonts', array());
  if (empty($fontsbuffer) || $reset) {
    $result = drupal_http_request('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCyG3Uj64W3QSdydE8Hia6gVeNv_PpAWn4');
    if ($result->code != 200) {
      drupal_set_message(t('The list of Google Fonts could not be fetched. Verify that your server can connect the Google Servers (https://www.googleapis.com). Error: %error', array('%error' => $result->error)), 'error');
    }
    elseif (isset($result->data)) {
      $tmp = json_decode($result->data);
      $fontsbuffer = array();
      foreach($tmp->items as $font) {
        $fontsbuffer[$font->family] = $font;
      }
      variable_set('weebpal_tools_google_fonts_webfonts', $fontsbuffer);
    }
  }
  
  return $fontsbuffer;
}

function weebpal_tools_google_fonts_load_font($font_family) {
  $fontsbuffer = weebpal_tools_google_fonts_available_fonts();
  return isset($fontsbuffer[$font_family]) ? $fontsbuffer[$font_family] : NULL;
}

function weebpal_tools_default_fonts_arr() {
  return array(
    'base_font' => 'Base font',
    'site_name_font' => 'Site name',
    'site_slogan_font' => 'Site slogan',
    'page_title_font' => 'Page title',
    'node_title_font' => 'Node title',
    'comment_title_font' => 'Comment title',
    'block_title_font' => 'Block title',
  );
}

function weebpal_tools_default_fonts_map() {
  return array(
    'base_font' => '#page',
    'site_name_font' => '#page .site-name',
    'site_slogan_font' => '#page .site-slogan',
    'page_title_font' => '#page #page-title',
    'node_title_font' => '#page .node > h2',
    'comment_title_font' => '#page .comment > h3',
    'block_title_font' => '#page .block > .block-inner > h2',
  );
}

function weebpal_tools_apply_google_webfont(&$vars) {
  $fonts_arr = weebpal_tools_default_fonts_arr();
  $element_map = weebpal_tools_default_fonts_map();
  $font_families = array();
  $font_subsets = array();
  foreach ($fonts_arr as $key => $title) {
    $font_family = theme_get_setting($key . "_family");
    $font_category = theme_get_setting($key . "_category");
    $font_variant = theme_get_setting($key . "_variant");
    $font_subset = theme_get_setting($key . "_subset");
    $font_version = theme_get_setting($key . "_version");
    $font_file = theme_get_setting($key . "_file");
    $font_value = theme_get_setting($key . (!empty($font_type) ? ('_' . $font_type) : ""));
    if(!empty($font_family)) {
      $font_families[] = str_replace(" ", "+", $font_family) . ":" . $font_variant;
      $font_subsets[] = $font_subset;
      $class = "gwf-" . str_replace("_", "-", $key);
      $vars['classes_array'][] = $class;
      drupal_add_css("body.$class " . $element_map[$key] . " { font-family: '" . $font_family . "', $font_category; }", array(
        'type' => 'inline'
      ));
    }
  }
  if(!empty($font_families)) {
    $url = "http://fonts.googleapis.com/css?family=" . implode("|", $font_families) . "&subset=" . implode(",", $font_subsets);
    drupal_add_css($url);  
  }
}

