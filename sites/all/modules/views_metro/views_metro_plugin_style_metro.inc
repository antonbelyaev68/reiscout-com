<?php
include_once drupal_get_path('module', 'views_metro') . '/views_metro.inc';

/**
 * @file
 * Contains the list style plugin.
 */

class views_metro_plugin_style_metro extends views_plugin_style_list {

  function option_definition() {
    $options = parent::option_definition();
    $options['menu'] = array('menu' => "");
    $options['number'] = array('number' => 9);
    return $options;
  }

  function options_form(&$form, &$form_state) {
    $form['menu'] = array(
      '#type' => 'select',
      '#title' => t('Menu'),
      '#description' => t('...'),
      '#default_value' => $this->options['menu'],
      '#options' => views_metro_get_menu_options(),
    );
    /*
    $form['number'] = array(
      '#type' => 'select',
      '#title' => t('Number'),
      '#description' => t('This number must be least or equal number row of this view.'),
      '#default_value' => $this->options['number'],
      '#options' => views_metro_get_number_options(),
    );
    */

    for($i = 1; $i < 10; $i ++) {
      $form['position_' . $i] = array(
        '#type' => 'select',
        '#title' => t('Position @i', array('@i' => $i)),
        '#description' => t('Position @i', array('@i' => $i)),
        '#default_value' => $this->options['position_' . $i],
        '#options' => views_metro_get_position_options(),
      );
    }
  }

  function options_validate(&$form, &$form_state) {
  }

  function options_submit(&$form, &$form_state) {
  }
}
