<?php

function views_metro_get_menus() {
  $query = db_select('menu_custom', 'm');
  $query->fields('m');
  $menus = $query->execute()->fetchAll();
  return $menus;
}

function views_metro_get_menu_options() {
  $menus = views_metro_get_menus();
  $results = array('' => t("-- Select a Menu --"));
  foreach ($menus as $menu) {
    $results[$menu->menu_name] = $menu->title;
  }
  return $results;
}

function views_metro_insert(&$arr, $element, $order) {
  for($i = count($arr); $i > $order; $i --) {
    $arr[$i] = $arr[$i - 1];
  }
  $arr[$order] = $element;
}

function views_metro_get_position_options() {
  $options = array();
  for($i = 0; $i < 10; $i ++) {
    $options[$i] = $i;
  };
  return $options;
}

function view_metro_get_positions($positions) {
  $results = array();
  for($i = 1; $i < 10; $i ++) {
    if(isset($positions['position_' . $i]) && $positions['position_' . $i]) {
      $results[] = $positions['position_' . $i];
    }
  }
  sort($results);
  return $results;
}
