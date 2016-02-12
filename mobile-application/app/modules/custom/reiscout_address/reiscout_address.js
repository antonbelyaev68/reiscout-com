/**
 * Implements hook_install().
 * This hook is used by modules that need to execute custom code when the module
 * is loaded. Note, the Drupal.user object is not initialized at this point, and
 * always appears to be an anonymous user.
 */
function reiscout_address_install() {
  try {
    var modulePath = drupalgap_get_path('module', 'reiscout_address');
    drupalgap_add_js('http://maps.googleapis.com/maps/api/js?libraries=places');
    drupalgap_add_js(modulePath + '/geocomplete/jquery.geocomplete' + (Drupal.settings.debug ? '' : '.min') + '.js');
    drupalgap_add_css(modulePath + '/reiscout_address.css');
  }
  catch (error) {
    console.log('reiscout_address_install - ' + error);
  }
}

/**
 * Implements hook_form_alter().
 * This hook is used to make alterations to existing forms.
 */
function reiscout_address_form_alter(form, form_state, form_id) {
  try {
    if (Drupal.settings.debug) {
      console.log(['reiscout_address_form_alter', form, form_state, form_id]);
    }

    if (form_id === 'user_login_form') {
      form.submit = ['_reiscout_address_user_login_form_submit'];

      if (typeof form.buttons.create_new_account !== 'undefined') {
        form.buttons.create_new_account.attributes.onclick = "drupalgap_goto('" + _reiscout_address_path_destination('user/register') + "')";
      }

      if (typeof form.buttons.forgot_password !== 'undefined') {
        form.buttons.forgot_password.attributes.onclick = "drupalgap_goto('" + _reiscout_address_path_destination('user/password') + "')";
      }
    }
    else if (form_id === 'user_register_form') {
      form.submit = ['_reiscout_address_user_register_form_submit'];
    }
    else if (form_id === 'user_pass_form') {
      form.submit = ['_reiscout_address_user_pass_form_submit'];
    }
    else if (form_id === 'node_edit' && form.bundle === 'property') {
      var address, position, delta;
      var language = language_default();
      var elements = form.elements;

      if (typeof elements.field_address_text !== 'undefined') {
        if (elements.field_address_text.type === 'text') {
          if (elements.field_address_text.field_info_instance.widget.type === 'text_textfield') {
            address = elements.field_address_text;
            address.field_info_instance.widget.module = 'reiscout_address';
            address.field_info_instance.widget.type = 'reiscout_geocomplete';
          }
          else {
            console.log('WARNING: reiscout_address_form_alter() - field field_address_text has unsupported widget type: ' + elements.field_address_text.field_info_instance.widget.type);
          }
        }
        else {
          console.log('WARNING: reiscout_address_form_alter() - field field_address_text has unsupported field type: ' + elements.field_address_text.type);
        }
      }
      else {
        console.log('WARNING: reiscout_address_form_alter() - field field_address_text is missing');
      }

      if (module_exists('geofield')) {
        if (typeof elements.field_geo_position !== 'undefined') {
          if (elements.field_geo_position.type === 'geofield') {
            if (elements.field_geo_position.field_info_instance.widget.type === 'geofield_latlon') {
              position = elements.field_geo_position;
              position.field_info_instance.widget.module = 'reiscout_address';
              position.field_info_instance.widget.type = 'reiscout_' + position.field_info_instance.widget.type;
              position.reiscout_address_id = '';
              
              if (address && typeof address[language] !== 'undefined') {
                for (delta in address[language]) {
                  if (address[language].hasOwnProperty(delta)) {
                    position.reiscout_address_id = address[language][delta].id;
                    break;
                  }
                }
              }

              if (!position.reiscout_address_id.length) {
                console.log('WARNING: reiscout_address_form_alter() - address id not found');
              }
            }
            else {
              console.log('WARNING: reiscout_address_form_alter() - field field_geo_position has unsupported widget type: ' + elements.field_geo_position.field_info_instance.widget.type);
            }
          }
          else {
            console.log('WARNING: reiscout_address_form_alter() - field field_geo_position has unsupported field type: ' + elements.field_geo_position.type);
          }
        }
        else {
          console.log('WARNING: reiscout_address_form_alter() - field field_geo_position is missing');
        }
      }
      else {
        console.log('WARNING: reiscout_address_form_alter() - module geofield is not installed');
      }
    }
  }
  catch (error) {
    console.log('reiscout_address_form_alter - ' + error);
  }
}

/**
 * Implements hook_field_widget_form().
 * @param {Object} form
 * @param {Object} form_state
 * @param {Object} field
 * @param {Object} instance
 * @param {String} langcode
 * @param {Object} items
 * @param {Number} delta
 * @param {Object} element
 */
function reiscout_address_field_widget_form(form, form_state, field, instance, langcode, items, delta, element) {
  try {
    if (Drupal.settings.debug) {
      console.log(['reiscout_address_field_widget_form', form, form_state, field, instance, langcode, items, delta, element]);
    }

    if (instance.widget.type === 'reiscout_geocomplete') {
      text_field_widget_form(form, form_state, field, instance, langcode, items, delta, element);

      items[delta].children.push({
        markup: drupalgap_jqm_page_event_script_code({
          page_id: drupalgap_get_page_id(),
          jqm_page_event: 'pageshow',
          jqm_page_event_callback: '_reiscout_address_geocomplete_field_pageshow',
          jqm_page_event_args: JSON.stringify({
              geocomplete_id: items[delta].id,
          })
        })
      });
    }
    else if (instance.widget.type === 'reiscout_geofield_latlon') {
      items[delta].type = 'hidden';

      if (items[delta].item) {
        items[delta].value = items[delta].item.lat + ',' + items[delta].item.lon;
      }

      items[delta].children.push({
        id: items[delta].id + '-btn',
        text: t('Get GPS Address'),
        type: 'button',
        options: {
          attributes: {
            onclick: "_reiscout_address_getposition_click('" + items[delta].id + "', '" + element.reiscout_address_id + "')"
          }
        }
      });
    }
    else {
      console.log('WARNING: reiscout_address_field_widget_form() - unsupported widget type: ' + instance.widget.type);
    }
  }
  catch (error) {
    console.log('reiscout_address_field_widget_form - ' + error);
  }
}

/**
 * Implements hook_entity_post_render_field().
 * Called after drupalgap_entity_render_field() assembles the field content
 * string. Use this to make modifications to the HTML output of the entity's
 * field before it is displayed. The field content will be inside of
 * reference.content, so to make modifications, change reference.content. For
 * more info: http://stackoverflow.com/questions/518000/is-javascript-a-pass-by-reference-or-pass-by-value-language
 */
function reiscout_address_entity_post_render_field(entity, field_name, field, reference) {
  try {
    if (Drupal.settings.debug) {
      console.log(['reiscout_address_entity_post_render_field', entity, field_name, field, reference]);
    }

    if (field.entity_type === 'node' && field.bundle === 'property') {
      if (field_name === 'field_address_text') {
        var display = field.display['default'];
        if (field.display['drupalgap']) {
          display = field.display['drupalgap'];
        }

        var label = '';
        if (display['label'] !== 'hidden') {
          label = field.label;
        }

        // TODO: find a proper way to get a field value and render it
        var value = '';
        var default_lang = language_default();
        var language = entity.language;
        if (entity[field_name]) {
          var items = null;
          if (entity[field_name][default_lang]) {
            items = entity[field_name][default_lang];
            language = default_lang;
          }
          else if (entity[field_name][language]) {
            items = entity[field_name][language];
          }
          else if (entity[field_name]['und']) {
            items = entity[field_name]['und'];
            language = 'und';
          }

          if (items && typeof items[0] !== 'undefined') {
            if (typeof items[0].safe_value !== 'undefined') {
              value = items[0].safe_value;
            }
            else if (typeof items[0].value !== 'undefined') {
              value = items[0].value;
            }
          }
        }

        // Author of the node with "edit own content" permission.
        // Function node_access also checks "edit any content" and this is why we do not use it here.
        if (entity.uid === Drupal.user.uid && user_access('edit own ' + entity.type + ' content')) {
          reference.content = theme('reiscout_address_editable', {
            bundle: field.bundle,
            nid: entity.nid,
            language: language,
            label: label,
            name: field_name,
            value: value
          });
        }
        else {
          reference.content = theme('reiscout_address_hidden', {
            label: label,
            name: field_name,
            value: value
          });
        }
      }
    }
  }
  catch (error) {
    console.log('reiscout_address_entity_post_render_field - ' + error);
  }
}

function _reiscout_address_getposition_click(position_id, address_id) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_getposition_click', position_id, address_id]);
    }
    
    drupalgap_loading_message_show({
      text: t('Getting position and address') + '...',
      textVisible: true,
      theme: 'b'
    });

    var alertMessage = t('Could not detect your position.') + (address_id.length ? ' ' + t('Please, enter address manually.') : '');

    navigator.geolocation.getCurrentPosition(
      function (position) {
        if (Drupal.settings.debug) {
          console.log(['_reiscout_address_getposition_click.getCurrentPosition', position]);
        }

        if (typeof position.coords.latitude !== 'undefined' && typeof position.coords.longitude !== 'undefined') {
          $('#' + position_id).val([position.coords.latitude, position.coords.longitude].join(','));

          if (address_id.length) {
            Drupal.services.call({
              method: 'POST',
              path: 'geocoderapi/geocode_reverse.json',
              service: 'geocoderapi',
              resource: 'geocode_reverse',
              data: JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
              }),
              success: function(result) {
                if (Drupal.settings.debug) {
                  console.log(['_reiscout_address_getposition_click.geocode_reverse.success', result]);
                }

                $('#' + address_id).val(result.address);
              },
              error: function(xhr, status, message) {
                if (Drupal.settings.debug) {
                  console.log(['_reiscout_address_getposition_click.geocode_reverse.error', xhr, status, message]);
                }

                drupalgap_alert(alertMessage);
              }
            });
          }
        }
        else {
          drupalgap_alert(alertMessage);
        }
      },
      function (error) {
        drupalgap_loading_message_hide();

        if (Drupal.settings.debug) {
          console.log(['_reiscout_address_getposition_click.getCurrentPosition', error]);
        }

        drupalgap_alert(alertMessage);
      },
      {enableHighAccuracy: true}
    );
  }
  catch (error) {
    console.log('_reiscout_address_getposition_click - ' + error);
  }
}

function _reiscout_address_geocomplete_field_pageshow(options) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_geocomplete_field_pageshow', options]);
    }

    var placeholder = $('#' + options.geocomplete_id).prop('placeholder');
    $('#' + options.geocomplete_id).geocomplete();
    $('#' + options.geocomplete_id).prop('placeholder', placeholder);
  }
  catch (error) {
    console.log('_reiscout_address_geocomplete_field_pageshow - ' + error);
  }
}

function _reiscout_address_editable_address_form_show(button) {
  try {
    var container = $(button).parent().parent().parent();
    container.find('#editable-form-value').val(container.find('.editable-view .editable-value').text());
    container.find('.editable-view, .editable-form').toggle();
  }
  catch (error) {
    console.log('_reiscout_address_editable_address_form_show - ' + error);
  }
}

function _reiscout_address_editable_address_form_save(button, nid, type, field, language) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_editable_address_form_save', nid, type, field, language]);
    }
    
    var container = $(button).parent().parent().parent();
    var input     = container.find('#editable-form-value');
    var value     = input.val();

    if (value.length) {
      Drupal.services.call({
        method: 'POST',
        path: 'editableapi/save.json',
        service: 'editableapi',
        resource: 'save',
        data: JSON.stringify({
          nid: nid,
          type: type,
          field: field,
          language: language,
          delta: 0,
          value: value
        }),
        success: function(result) {
          if (Drupal.settings.debug) {
            console.log(['_reiscout_address_editable_address_form_save.success', result]);
          }

          container.find('.editable-view .editable-value').text(result.value);
          $('#node_' + nid + '_view_container h2:eq(0)').text(result.value);
          container.find('.editable-view, .editable-form').toggle();
          input.val('');
        },
        error: function(xhr, status, message) {
          if (Drupal.settings.debug) {
            console.log(['_reiscout_address_editable_address_form_save.error', xhr, status, message]);
          }

          if (message) {
            try {
              message = JSON.parse(message);
              if (typeof message === 'string') {
                drupalgap_alert(message);
              }
              else if (message instanceof Array) {
                drupalgap_alert(message.join("\n"));
              }
              else {
                drupalgap_alert(t('Unexpected error occurred'));
              }
            }
            catch (error) {
              drupalgap_alert(t('Unexpected error occurred'));
            }
          }
        }
      });
    }
    else {
      container.find('.editable-view, .editable-form').toggle();
      input.val('');
    }
  }
  catch (error) {
    console.log('_reiscout_address_editable_address_form_save - ' + error);
  }
}

function _reiscout_address_editable_address_form_cancel(button) {
  try {
    var container = $(button).parent().parent().parent();
    container.find('.editable-view, .editable-form').toggle();
    container.find('#editable-form-value').val('');
  }
  catch (error) {
    console.log('_reiscout_address_editable_address_form_cancel - ' + error);
  }
}

function _reiscout_address_property_address_pageshow(options) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_property_address_pageshow', options]);
    }

    $(options.selector).data('address', options.address);
  }
  catch (error) {
    console.log('_reiscout_address_property_address_pageshow - ' + error);
  }
}

function _reiscout_address_user_request_address_info(button) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_user_request_address_info']);
    }

    if (!Drupal.user.uid) {
      _reiscout_address_goto('user/login');
    }
    else {
      var container = $(button).parent().parent();
      container.find('.address-value').text(container.find('.address-value').data('address'));
      container.find('.address-button').hide();
    }
  }
  catch (error) {
    console.log('_reiscout_address_user_request_address_info - ' + error);
  }
}

function _reiscout_address_goto(path, destination) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_goto', path, destination]);
    }

    path = _reiscout_address_path_destination(path, destination ? destination : drupalgap_path_get());

    drupalgap_goto(path);
  }
  catch (error) {
    console.log('_reiscout_address_goto - ' + error);
  }
}

function _reiscout_address_goto_destination(path, forward) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_goto_destination', path, forward]);
    }

    var destination = _GET('destination');
    
    if (destination) {
      path = (forward ? _reiscout_address_path_destination(path, destination) : destination);
    }
    else if (typeof path === 'undefined' || !path.length) {
      path = drupalgap.settings.front;
    }

    drupalgap_goto(path);
  }
  catch (error) {
    console.log('_reiscout_address_goto_destination - ' + error);
  }
}

function _reiscout_address_path_destination(path, destination) {
  try {
    if (Drupal.settings.debug) {
      console.log(['_reiscout_address_path_destination', path, destination]);
    }
    
    if (typeof path === 'undefined' || !path.length) {
      path = drupalgap.settings.front;
    }

    if (!destination) {
      destination = _GET('destination');
    }

    if (destination) {
      path += '?destination=' + destination;
    }

    return path;
  }
  catch (error) {
    console.log('_reiscout_address_path_destination - ' + error);
  }
}