<?php

/**
 * Display the simple view of rows one after another
 */
function template_preprocess_views_metro(&$variables) {
  $view = $variables['view'];
  $options = $view->style_plugin->options;
  $menu_name = $options['menu'];
  $positions = view_metro_get_positions($options);
  // var_dump($positions);
  $items_page = menu_tree_page_data($menu_name);
  $rows = array();
  foreach($items_page as $element) {
    $rows[] = l($element['link']['link_title'], $element['link']['href'], $element['link']['localized_options']);
  }
  $inserted_rows = array();
  for($i = 0; $i < 18 && $i < count($rows) - 1; $i += 2) {
    $r = array();
    $r[] = '<ul>';
    for($j = $i; $j < $i + 2; $j ++) {
      $r[] = '<li>';
      $r[] = $rows[$j];
      $r[] = '</li>';
    }
    $r[] = '</ul>';
    $inserted_rows[] = implode("\n", $r);
  }
  for($i = 0; $i < 9; $i ++) {
    if (count($inserted_rows) > $i && count($positions) > $i) {
      views_metro_insert($variables['rows'], $inserted_rows[$i], $positions[$i]-1);
    }
  }
  for($i = count($variables['rows']) - 1; $i >= 9; $i --) {
    if(isset($variables['rows'][$i])) {
      unset($variables['rows'][$i]);
    }
  }
}

