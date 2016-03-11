<?php
include_once(drupal_get_path('theme', 'real_estast') . '/common.inc');

function real_estast_theme() {
  $items = array();
  $items['render_panel'] = array(
    "variables" => array(
      'page' => array(),
      'panels_list' => array(),
      'panel_regions_width' => array(),
    ),
    'preprocess functions' => array(
      'real_estast_preprocess_render_panel'
    ),
    'template' => 'panel',
    'path' => drupal_get_path('theme', 'real_estast') . '/tpl',
  );
  return $items;
}

function weebpal_default_fonts_arr() {
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

function weebpal_default_fonts_map() {
  return array(
    'base_font' => '#page',
    'site_name_font' => '#page #site-name',
    'site_slogan_font' => '#page #site-slogan',
    'page_title_font' => '#page #page-title',
    'node_title_font' => '#page .node-content > h2',
    'comment_title_font' => '#page .comment > h3',
    'block_title_font' => '#page .block > h2',
  );
}

function weebpal_apply_google_webfont(&$vars) {
  $fonts_arr = weebpal_default_fonts_arr();
  $element_map = weebpal_default_fonts_map();
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

function real_estast_preprocess_html(&$vars) {
  $current_skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $current_skin = $_COOKIE['weebpal_skin'];
  }
  if (!empty($current_skin) && $current_skin != 'default') {
    $vars['classes_array'][] = "skin-$current_skin"; 
  }

  $current_background = theme_get_setting('background');
  if (isset($_COOKIE['weebpal_background'])) {
    $current_background = $_COOKIE['weebpal_background'];
  }
  if (!empty($current_background)) {
    $vars['classes_array'][] = $current_background;
  }

  $animation = 'animation-' . theme_get_setting('animation');
  $vars['classes_array'][] = $animation;
  weebpal_apply_google_webfont($vars);
}

function real_estast_preprocess_page(&$vars) {
  global $theme_key;
  $vars['regions_width'] = real_estast_regions_width($vars['page']);
  $panel_regions = real_estast_panel_regions();
  if (count($panel_regions)) {
    foreach ($panel_regions as $panel_name => $panels_list) {
      $panel_markup = theme("render_panel", array(
        'page' => $vars['page'],
        'panels_list' => $panels_list,
        'regions_width' => $vars['regions_width'],
      ));
      $panel_markup = trim($panel_markup);
      $vars['page'][$panel_name] = empty($panel_markup) ? FALSE : array('content' => array('#markup' => $panel_markup));
    }
  }
  $current_skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $current_skin = $_COOKIE['weebpal_skin'];
  }

  if (isset($vars['node']) && $vars['node']->type != 'page' && !in_array('page__node__delete', $vars['theme_hook_suggestions'])) {
    $result = db_select('node_type', NULL, array('fetch' => PDO::FETCH_ASSOC))
    ->fields('node_type', array('name'))
    ->condition('type', $vars['node']->type)
    ->execute()->fetchField();
    $vars['title'] = $result;
  }

  $vars['page']['show_skins_menu'] = $show_skins_menu = theme_get_setting('show_skins_menu');
  if($show_skins_menu) {
    $skins = real_estast_get_predefined_param('skins', array("default" => t("Default Style")));
    $current_skin = theme_get_setting('skin');
    if (isset($_COOKIE['weebpal_skin'])) {
      $current_skin = $_COOKIE['weebpal_skin'];
    }

    $backgrounds = real_estast_get_predefined_param('backgrounds', array("bg-default" => t("Default")));
    $current_background = theme_get_setting('background');
    if (isset($_COOKIE['weebpal_background'])) {
      $current_background = $_COOKIE['weebpal_background'];
    }

    $layouts = real_estast_get_predefined_param('layout', array("layout-default" => t("Default Layout")));
    $current_layout = theme_get_setting('layout');
    if (isset($_COOKIE['weebpal_layout'])) {
      $current_layout = $_COOKIE['weebpal_layout'];
    }

    $vars['page_css'] = '';
    $layout_width = (theme_get_setting('layout_width') == '') ? '1400' : theme_get_setting('layout_width');
    if ($current_layout == 'layout-boxed') {
      $vars['page_css'] = 'style="width:' . $layout_width . 'px;margin: 0 auto;"';
    }

    $str = array();
    $str[] = '<div id="change_skin_menu_wrapper" class="change-skin-menu-wrapper wrapper">';
    $str[] = '<div class="container">';
    $str[] = '<a id="change-skin" class="change-skin" href="javascript:void(0)"><i class="fa fa-cog"></i></a>';
    //Change skin color
    $str[] = '<div class="skin-color">';
    $str[] = '<strong>Skin</strong>';
    $str[] = '<ul class="change-skin-menu">';
    
    foreach ($skins as $skin => $skin_title) {
      $li_class = ($skin == $current_skin ? ($skin . ' active') : $skin);
      $str[] = '<li class="' . $li_class . '"><a href="#change-skin/' . $skin . '" class="change-skin-button color-' . $skin . '">' . $skin_title . '</a></li>';
    }
    $str[] = '</ul></div>';
    //Change Layout
    $str[] = '<div class="layout">';
    $str[] = '<strong>Layout Boxed</strong>';
    $str[] = '<label class="switch-btn">';
    foreach ($layouts as $layout => $layout_title) {
      $label_class = ($layout == $current_layout ? ' active' : '');
      $label_title = ($layout == 'layout-boxed' ? 'Yes' : 'No');
      $str[] = '<span id="' . $layout . '" class="btn btn-default change-layout-button ' . $label_class . '">' . $label_title . '</span>';
    }
    $str[] = '</label></div>';
    //Change Background
    $str[] = '<div class="background">';
    $str[] = '<strong>Background</strong>';
    $str[] = '<ul class="change-background">';

    foreach ($backgrounds as $background => $background_title) {
      $li_class = ($background == $current_background ? ($background . ' active') : $background);
      $str[] = '<li class="' . $li_class . '"><a href="#change-background/' . $background . '" class="change-background-button">' . $background_title . '</a></li>';
    }

    $str[] = '</ul></div></div></div>';
    $vars['page']['show_skins_menu'] = implode("", $str);
  }
  drupal_add_js('
    (function ($) {
      Drupal.behaviors.skinMenuAction = {
        attach: function (context) {
          jQuery(".change-skin-button").click(function() {
            parts = this.href.split("/");
            style = parts[parts.length - 1];
            jQuery.cookie("weebpal_skin", style, {path: "' . base_path() . '"});
            window.location.reload();
            return false;
          });
          jQuery(".change-background-button").on("click", function() {
            parts = this.href.split("/");
            style = parts[parts.length - 1];
            var current_background = jQuery.cookie("weebpal_background");
            jQuery.cookie("weebpal_background", style, {path: "' . base_path() . '"});
            jQuery("body").removeClass(current_background);
            jQuery("body").addClass(style);
            return false;
          });
          jQuery(".change-layout-button").on("click", function() {
            var layout_class = $(this).attr("id");
            var layout_width = ' . $layout_width . ';
            jQuery.cookie("weebpal_layout", layout_class, {path: "' . base_path() . '"});
            jQuery("#page").removeAttr("style");
            if (layout_class != "layout-default") {
              jQuery("#page").css("width", layout_width);
              jQuery("#page").css("margin", "0 auto");
            }
            $(".change-layout-button").removeClass("active");
            if(!$(this).hasClass("active"))
                $(this).addClass("active");
            return false;
          });
        }
      }
    })(jQuery);
  ', 'inline');
  $vars['page']['weebpal_skin_classes'] = !empty($current_skin) ? ($current_skin . "-skin") : "";
  if (!empty($current_skin) && $current_skin != 'default' && theme_get_setting("default_logo") && theme_get_setting("toggle_logo")) {
    $vars['logo'] = file_create_url(drupal_get_path('theme', $theme_key)) . "/css/colors/" . $current_skin . "/images/logo.png";
  }
}

function real_estast_preprocess_node(&$vars) {
  $vars['real_estast_media_field'] = false;
  foreach($vars['content'] as $key => $field) {
    if (isset($field['#field_type']) && isset($field['#weight'])) {
      if ($field['#field_type'] == 'image' || $field['#field_type'] == 'video_embed_field' || $field['#field_type'] == 'youtube') {
        $vars['real_estast_media_field'] = drupal_render($field);
        $vars['classes_array'][] = 'real-estast-media-first';
        unset($vars['content'][$key]);
        break;
      }
    }
  }
  if (theme_get_setting('node_animation')) {
    $vars['classes_array'][] = 'animation';
  }
  $vars['page'] = ($vars['type'] == 'page') ? TRUE : FALSE;
  $vars['created_day'] = date('d', $vars['created']);
  $vars['created_month'] = date('M', $vars['created']);
  $vars['created_year'] = date('M', $vars['created']);
  
  if(isset($vars['content']['links']['comment'])) {
    $vars['comment_links'] = $vars['content']['links']['comment'];
    unset($vars['content']['links']['comment']);
  }
}

function real_estast_preprocess_render_panel(&$variables) {
  $page = $variables['page'];
  $panels_list = $variables['panels_list'];
  $regions_width = $variables['regions_width'];
  $variables = array();
  $variables['page'] = array();
  $variables['panel_width'] = $regions_width;
  $variables['panel_classes'] = array();
  $variables['panels_list'] = $panels_list;
  $is_empty = TRUE;
  $panel_keys = array_keys($panels_list);

  foreach ($panels_list as $panel) {
    $variables['page'][$panel] = $page[$panel];
    $panel_width = $regions_width[$panel];
    if (render($page[$panel])) {
      $is_empty = FALSE;
    }
    $classes = array("panel-column");
    $classes[] = "col-lg-$panel_width";
    $classes[] = "col-md-$panel_width";
    $classes[] = "col-sm-12";
    $classes[] = "col-xs-12";
    $classes[] = str_replace("_", "-", $panel);
    $variables['panel_classes'][$panel] = implode(" ", $classes);
  }
  $variables['empty_panel'] = $is_empty;
}

function real_estast_css_alter(&$css) {
  global $theme_key;
  $skin = theme_get_setting('skin');
  if (isset($_COOKIE['weebpal_skin'])) {
    $skin = $_COOKIE['weebpal_skin'] == 'default' ? '' : $_COOKIE['weebpal_skin'];
  }
  if (!empty($skin) && file_exists(drupal_get_path('theme', $theme_key) . "/css/colors/" . $skin . "/style.css")) {
    $css = drupal_add_css(drupal_get_path('theme', $theme_key) . "/css/colors/" . $skin . "/style.css", array(
      'group' => CSS_THEME,
    ));
  }


}

function real_estast_preprocess_maintenance_page(&$vars) {
}

function real_estast_preprocess_views_view_fields(&$vars) {
  $view = $vars['view'];
  foreach ($vars['fields'] as $id => $field) {
    if(isset($field->handler->field_info) && $field->handler->field_info['type'] === 'image') {
      $prefix = $field->wrapper_prefix;
      if(strpos($prefix, "views-field ") !== false) {
        $parts = explode("views-field ", $prefix);
        $type = str_replace("_", "-", $field->handler->field_info['type']);
        $prefix = implode("views-field views-field-type-" . $type . " ", $parts);
      }
      $vars['fields'][$id]->wrapper_prefix = $prefix;
    }
  }
}

function real_estast_node_view_alter(&$build) {
  if ($build['#view_mode'] =='teaser' && $build['#bundle'] == 'product') {
    unset($build['links']['comment']);
  }
}

function real_estast_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];

  if (!empty($breadcrumb)) {
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    $output .= '<div class="breadcrumb">' . implode('<span>»</span>', $breadcrumb) . '</div>';
    return $output;
  }
}

function real_estast_preprocess_field(&$vars) {
  if($vars['element']['#field_name'] == 'field_latitude') {
    $node = $vars['element']['#object'];
    if(!empty($node->field_latitude[$node->language][0]['value']) 
       && !empty($node->field_longitude[$node->language][0]['value'])) {
      $lat = $node->field_latitude[$node->language][0]['value'];
      $long = $node->field_longitude[$node->language][0]['value'];
      $markers = array();
      $vars['view_id'] = 'gmap-field-group-map';
      $vars['width'] = "787px";
      $vars['height'] = "400px";
      drupal_add_js("https://maps.googleapis.com/maps/api/js?v=3.exp");
      drupal_add_js('
        (function ($) {
         Drupal.realEstastField = Drupal.realEstastField || {};
         Drupal.behaviors.realEstateMapAction = {
            attach: function (context) {
              $(window).load(function() {
                  Drupal.realEstastField.myLatlng = new google.maps.LatLng(' . $lat . ',' . $long . ');
                  Drupal.realEstastField.mapOptions = {
                    zoom: 14,
                    center: Drupal.realEstastField.myLatlng
                  }
                  Drupal.realEstastField.map = new google.maps.Map(document.getElementById("' . $vars['view_id'] . '"), Drupal.realEstastField.mapOptions);
                  Drupal.realEstastField.marker = new google.maps.Marker({
                    position: Drupal.realEstastField.myLatlng,
                      map: Drupal.realEstastField.map
                  });      
              });
            }
          }
        })(jQuery);
        ', 'inline');     
    }
  }
  elseif($vars['element']['#field_name'] == 'field_longitude') {
    $node = $vars['element']['#object'];
    if(!empty($node->field_property_lattitude[$node->language][0]['value']) && !empty($node->field_property_longitude[$node->language][0]['value'])) {
      $vars['classes_array'][] = 'hidden';
    }
  }
}
