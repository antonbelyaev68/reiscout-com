<?php
/**
 * @file
 * Theme setting callbacks for the weebpal theme.
 */
include_once(drupal_get_path('theme', 'real_estast') . '/common.inc');

function real_estast_reset_settings() {
  global $theme_key;
  variable_del('theme_' . $theme_key . '_settings');
  variable_del('theme_settings');
  $cache = &drupal_static('theme_get_setting', array());
  $cache[$theme_key] = NULL;
}

// Impliments hook_form_system_theme_settings_alter().
function real_estast_form_system_theme_settings_alter(&$form, $form_state) {
  if (theme_get_setting('real_estast_use_default_settings')) {
    real_estast_reset_settings();
  }
  $form['#attached']['js'][] = array(
    'data' => drupal_get_path('theme', 'real_estast') . '/js/real_estast.js',
    'type' => 'file',
  );
  $form['real_estast']['real_estast_version'] = array(
    '#type' => 'hidden',
    '#default' => '1.0',
  );
  real_estast_settings_layout_tab($form);
  real_estast_feedback_form($form);
  $form['#submit'][] = 'real_estast_form_system_theme_settings_submit';
}

function real_estast_settings_layout_tab(&$form) {
  global $theme_key;
  $skins = real_estast_get_predefined_param('skins', array('' => t("Default skin")));
  $backgrounds = real_estast_get_predefined_param('backgrounds', array('bg-default' => t("Default")));
  $animations = real_estast_get_predefined_param('animations', array('none' => t("None")));
  $layout = real_estast_get_predefined_param('layout', array('layout-default' => t("Default Layout")));

  $form['real_estast']['settings'] = array(
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#title' => t('Settings'),
    '#weight' => 0,
  );

  if (count($skins) > 1 && count($backgrounds) > 1) {
    $form['real_estast']['settings']['configs'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#title' => t('Configs'),
      '#weight' => 0,
    );
  	$form['real_estast']['settings']['configs']['skin'] = array(
      '#type' => 'select',
      '#title' => t('Skin'),
      '#default_value' => theme_get_setting('skin'),
      '#options' => $skins,
      '#weight' => 0,
    );
    $form['real_estast']['settings']['configs']['background'] = array(
      '#type' => 'select',
      '#title' => t('Background'),
      '#default_value' => theme_get_setting('background'),
      '#options' => $backgrounds,
      '#weight' => 1,
    );
  }

  $form['real_estast']['settings']['configs']['effect'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
    '#title' => t('Theme Effect'),
    '#weight' => 3,
  );

  $form['real_estast']['settings']['configs']['effect']['animation'] = array(
    '#type' => 'select',
    '#title' => t('Animation'),
    '#default_value' => theme_get_setting('animation'),
    '#options' => $animations,
    '#weight' => 0,
  );

  $form['real_estast']['settings']['configs']['effect']['node_animation'] = array(
    '#type' => 'checkbox',
    '#title' => t('If checked, the animation will apply to the node.'),
    '#default_value' => theme_get_setting('node_animation'),
    '#weight' => 2,
  );

  $form['real_estast']['settings']['configs']['layout'] = array(
    '#type' => 'select',
    '#title' => t('Layout'),
    '#default_value' => theme_get_setting('layout'),
    '#options' => $layout,
    '#weight' => -2,
  );

  $default_layout_width = (theme_get_setting('layout_width') == '') ? '1400' : theme_get_setting('layout_width');
  $form['real_estast']['settings']['configs']['layout_width'] = array(
    '#type' => 'textfield',
    '#title' => t('Layout Width(px)'),
    '#default_value' => $default_layout_width,
    '#size' => 15,
    '#require' => TRUE,
    '#weight' => -1,
    '#states' => array(
      'visible' => array(
        'select[name="layout"]' => array(
          'value' => 'layout-boxed',
        ),
      ),
    ),
  );


  $form['theme_settings']['toggle_logo']['#default_value'] = theme_get_setting('toggle_logo');
  $form['theme_settings']['toggle_name']['#default_value'] = theme_get_setting('toggle_name');
  $form['theme_settings']['toggle_slogan']['#default_value'] = theme_get_setting('toggle_slogan');
  $form['theme_settings']['toggle_node_user_picture']['#default_value'] = theme_get_setting('toggle_node_user_picture');
  $form['theme_settings']['toggle_comment_user_picture']['#default_value'] = theme_get_setting('toggle_comment_user_picture');
  $form['theme_settings']['toggle_comment_user_verification']['#default_value'] = theme_get_setting('toggle_comment_user_verification');
  $form['theme_settings']['toggle_favicon']['#default_value'] = theme_get_setting('toggle_favicon');
  $form['theme_settings']['toggle_secondary_menu']['#default_value'] = theme_get_setting('toggle_secondary_menu');
  $form['theme_settings']['show_skins_menu'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show Skins Menu'),
    '#default_value' => theme_get_setting('show_skins_menu'),
  );

  $form['logo']['default_logo']['#default_value'] = theme_get_setting('default_logo');
  $form['logo']['settings']['logo_path']['#default_value'] = theme_get_setting('logo_path');
  $form['favicon']['default_favicon']['#default_value'] = theme_get_setting('default_favicon');
  $form['favicon']['settings']['favicon_path']['#default_value'] = theme_get_setting('favicon_path');
  $form['theme_settings']['#collapsible'] = TRUE;
  $form['theme_settings']['#collapsed'] = FALSE;
  $form['logo']['#collapsible'] = TRUE;
  $form['logo']['#collapsed'] = FALSE;
  $form['favicon']['#collapsible'] = TRUE;
  $form['favicon']['#collapsed'] = FALSE;
  $form['real_estast']['settings']['theme_settings'] = $form['theme_settings'];
  $form['real_estast']['settings']['theme_settings']['#weight'] = 10;
  $form['real_estast']['settings']['logo'] = $form['logo'];
  $form['real_estast']['settings']['logo']['#weight'] = 30;
  $form['real_estast']['settings']['favicon'] = $form['favicon'];
  $form['real_estast']['settings']['favicon']['#weight'] = 40;

  unset($form['theme_settings']);
  unset($form['logo']);
  unset($form['favicon']);

  if(module_exists('weebpal_tools')) {
    module_load_include('inc', 'weebpal_tools', 'weebpal_tools.functions');
    $fonts_arr = weebpal_tools_default_fonts_arr();
    $gwf = weebpal_tools_google_fonts_available_fonts(true);
    $form['real_estast']['settings']['gwf'] = array(
      '#type' => 'fieldset',
      '#title' => t('Typography'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#weight' => 20,
    );

    $family_options = array("" => t("-- Select Font --"));
    $subset_options = array("" => t("-- Select Subset --"));
    $variant_options = array("" => t("-- Select Variant --"));
    $files_options = array("" => t("-- Select Files --"));

    $category_list = array();
    foreach($gwf as $font) {
      $family_options[$font->family] = $font->family;
      foreach($font->subsets as $k => $v) {
        $subset_options[$v] = $v;
      }
      foreach($font->variants as $k => $v) {
        $variant_options[$v] = $v;
      }
      foreach($font->files as $k => $v) {
        $files_options[$k] = $v;
      }
    }

    foreach ($fonts_arr as $key => $title) {
      $form['real_estast']['settings']['gwf'][$key] = array(
        '#type' => 'fieldset',
        '#title' => t($title),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      );
      $font = weebpal_tools_google_fonts_load_font(theme_get_setting($key . "_family"));
      if($font) {
        foreach($font->subsets as $k => $v) {
          $subset_options[$v] = $v;
        }
        foreach($font->variants as $k => $v) {
          $variant_options[$v] = $v;
        }
        foreach($font->files as $k => $v) {
          $files_options[$k] = $v;
        }
      }
      $form['real_estast']['settings']['gwf'][$key][$key . "_family"] = array(
        '#type' => 'select',
        '#title' => t("Font Family"),
        '#options' => $family_options,
        '#default_value' => theme_get_setting($key . "_family"),
        '#attributes' => array('class' => array('font-family'), 'size' => '', 'maxlength' => ''),
      );

      $form['real_estast']['settings']['gwf'][$key][$key . "_group"] = array(
        '#type' => 'container',
        '#states' => array(
          'invisible' => array(
            'select[name="' . $key . '_family"]' => array(
              'value' => '',
            ),
          ),
        ),
      );    
      $form['real_estast']['settings']['gwf'][$key][$key . "_group"][$key . "_category"] = array(
        '#type' => 'textfield',
        '#title' => t("Font Category"),
        '#default_value' => theme_get_setting($key . "_category"),
        '#attributes' => array('class' => array('font-category'), 'size' => '', 'maxlength' => '', 'readonly' => array('readonly')),
      );
      $form['real_estast']['settings']['gwf'][$key][$key . "_group"][$key . "_variant"] = array(
        '#type' => 'select',
        '#title' => t("Font Variant"),
        '#options' => $variant_options,
        '#default_value' => theme_get_setting($key . "_variant"),
        '#attributes' => array('class' => array('font-variant'), 'size' => '', 'maxlength' => ''),
      );
      $form['real_estast']['settings']['gwf'][$key][$key . "_group"][$key . "_subset"] = array(
        '#type' => 'select',
        '#title' => t("Font Subset"),
        '#options' => $subset_options,
        '#default_value' => theme_get_setting($key . "_subset"),
        '#attributes' => array('class' => array('font-subset'), 'size' => '', 'maxlength' => ''),
      );
      $form['real_estast']['settings']['gwf'][$key][$key . "_group"][$key . "_version"] = array(
        '#type' => 'textfield',
        '#title' => t("Font Version"),
        '#default_value' => theme_get_setting($key . "_version"),
        '#attributes' => array('class' => array('font-version'), 'size' => '', 'maxlength' => '', 'readonly' => array('readonly')),
      );
      $form['real_estast']['settings']['gwf'][$key][$key . "_group"][$key . "_file"] = array(
        '#type' => 'select',
        '#title' => t("Font File"),
        '#options' => $files_options,
        '#default_value' => theme_get_setting($key . "_file"),
        '#attributes' => array('class' => array('font-file'), 'size' => '', 'maxlength' => ''),
      );
    }  

    $form['#attached']['js'][] = array(
      'data' => drupal_get_path('module', 'weebpal_tools') . '/js/weebpal_tools.gwf.js',
      'type' => 'file',
    );
    $form['#attached']['css'][] = array(
      'data' => drupal_get_path('theme', 'real_estast') . '/css/theme-settings.css',
      'type' => 'file',
      'group' => CSS_THEME,
    );  
    drupal_add_js(array('ajax_link' => (variable_get('clean_url', 0) ? '' : '?q=')), 'setting');    
  }
  $form['real_estast']['real_estast_use_default_settings'] = array(
    '#type' => 'hidden',
    '#default_value' => 0,
  );
  $form['actions']['real_estast_use_default_settings_wrapper'] = array(
    '#markup' => '<input type="submit" value="' . t('Reset theme settings') . '" class="form-submit form-reset" onclick="return Drupal.Light.onClickResetDefaultSettings();" style="float: right;">',
  );
}

function real_estast_feedback_form(&$form) {
  $form['real_estast']['about_real_estast'] = array(
    '#type' => 'fieldset',
    '#title' => t('Feedback Form'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#weight' => 40,
  );

  $form['real_estast']['about_real_estast']['about_real_estast_wrapper'] = array(
    '#type' => 'container',
    '#attributes' => array('class' => array('about-real_estast-wrapper')),
  );

  $form['real_estast']['about_real_estast']['about_real_estast_wrapper']['about_real_estast_content'] = array(
    '#markup' => '<iframe width="100%" height="650" scrolling="no" class="weebpal_frame" frameborder="0" src="http://www.weebpal.com/static/feedback/"></iframe>',
  );
}

function real_estast_form_system_theme_settings_submit($form, &$form_state) {
  if (isset($form_state['input']['skin']) && $form_state['input']['skin'] != $form_state['complete form']['real_estast']['settings']['configs']['skin']['#default_value']) {
    setcookie('weebpal_skin', $form_state['input']['skin'], time() + 100000, base_path());
  }
  if (isset($form_state['input']['background']) && $form_state['input']['background'] !== $form_state['complete form']['real_estast']['settings']['configs']['background']['#default_value']) {
    setcookie('weebpal_background', $form_state['input']['background'], time() + 100000, base_path());
  }

  if (isset($form_state['input']['layout']) && $form_state['input']['layout'] !== $form_state['complete form']['real_estast']['settings']['configs']['layout']['#default_value']) {
    setcookie('weebpal_layout', $form_state['input']['layout'], time() + 100000, base_path());
  }
}
