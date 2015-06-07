<?php
require_once 'config.php';

/**
 * @link              http://TBD.com
 * @since             1.0.0
 * @package           Makae_GM
 *
 * @wordpress-plugin
 * Plugin Name:       STF2015 HOOKS
 * Plugin URI:        http://TBD.com/MAKAE_GM/
 * Description:       The Makae Google Maps extension for timetable markers
 * Version:           1.0.0
 * Author:            Martin Käser
 * Author URI:        http://TBD.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mgm-stf
 */

function stf2015_hooks_make_menu() {
  $location_config = unserialize(STF2015_LOCATION_CONFIG);

  foreach($location_config as $key => $config) {
    $params = array(
      'makae-map' => $config['mapid'],
      'lat' => $config['coords']['latitude'],
      'lng' => $config['coords']['longitude']
    );
    $location_config[$key]['url'] = get_site_url() . '?' . http_build_query($params);
  }

  $html = '';
  $html .=
    '<div class="mgm-stf-position-wrapper">' .
      '<h5>' . __('Position', 'mgm-stf2015') . '</h5>' .
      '<ul class="mgm-stf-menu-position stf2015-map-menu">' .
        '<li class="stf2015-target toggle"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Wo bin ich?', 'mgm-stf2015') . '</span></li>' .
      '</ul>' .
    '</div>';

  $html .=
    '<h5>' . __('Kartentyp', 'mgm-stf2015') . '</h5>' .
    '<ul class="mgm-stf-display-option-list stf2015-map-menu">' .
      '<li class="option stf2015-overlay active" data-map-type-key="ROADMAP" data-map-overlay="true"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Overlay', 'mgm-stf2015') . '</span></li>' .
      '<li class="option stf2015-map" data-map-type-key="ROADMAP" data-map-overlay="false"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Map', 'mgm-stf2015') . '</span></li>' .
      '<li class="option stf2015-satellite" data-map-type-key="SATELLITE" data-map-overlay="false"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Satellite', 'mgm-stf2015') . '</span></li>' .
    '</ul>';

  $html .=
    '<h5>' . __('Orte', 'mgm-stf2015') . '</h5>' .
    '<ul class="mgm-stf-menu-links stf2015-map-menu">';
  foreach($location_config as $key => $config) {
    $html .= '<li class="stf2015-' . $key .' stf2015-location-link" data-config="'. urlencode(json_encode($config)) .'">' .
                '<a href="' . $config['url'] . '">' .
                 '<span class="map-menu-icon"></span>' .
                 '<span class="map-menu-label">' . $config['label'] . '</span>' .
                 '</a>' .
              '</li>';
  }
  $html .= '</ul>';

  return $html;
}

function stf2015_admin_enqueue_scripts() {
  wp_enqueue_script('stf2015_admin', plugin_dir_url(__FILE__) . 'js/mgm-stf2015-admin.js', array('makae-gm_init'), 1, true);

  $js_config = unserialize(STF2015_JS_CONFIG);
  wp_localize_script('stf2015_admin', 'mgm_stf_config', $js_config);
}

function stf2015_enqueue_scripts() {
  wp_enqueue_style('stf2015', plugin_dir_url(__FILE__) . 'css/mgm-stf.css');
  wp_enqueue_script('stf2015', plugin_dir_url(__FILE__) . 'js/mgm-stf2015.js', array('makae-gm_init'), 1, true);

  $js_config = unserialize(STF2015_JS_CONFIG);
  wp_localize_script('stf2015', 'mgm_stf_config', $js_config);
}

function stf2015_add_metabox() {
  add_meta_box(
    'stf2015_bad_weather',
    'Schlechtwetter Programm',
    'stf2015_meta_box_callback',
    'makae-map',
    'side'
  );
}

function stf2015_meta_box_callback( $post ) {

  // Add a nonce field so we can check for it later.
  wp_nonce_field('stf2015_bad_weather_meta_box', 'stf2015_bad_weather_meta_box_nonce');

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta($post->ID, '_bad_weather_program', true );
  $checked = $value == '1' ? ' checked="checked" ' : '';

  echo '<input type="checkbox" id="stf2015_bad_weather_switch" name="stf2015_bad_weather_switch" ' . $checked . ' value="1" />';
  echo '<label for="stf2015_bad_weather_switch"> Ja, leider schlecht Wetter </label> ';
  echo '<input type="hidden" name="stf2015_bad_weather_switch_submitted" value="1" />';
}

function stf2015_save_meta_box_data( $post_id ) {

  /*
   * We need to verify this came from our screen and with proper authorization,
   * because the save_post action can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['stf2015_bad_weather_meta_box_nonce'] ) ) {
    return;
  }

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['stf2015_bad_weather_meta_box_nonce'], 'stf2015_bad_weather_meta_box' ) ) {
    return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  // Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'makae-map' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }

  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }

  /* OK, it's safe for us to save the data now. */

  // Make sure that it is set.
  if ( ! isset( $_POST['stf2015_bad_weather_switch_submitted'] ) ) {
    return;
  }

  // Sanitize user input.
  $active = sanitize_text_field( $_POST['stf2015_bad_weather_switch'] );

  $active = $active == '1';
  // Update the meta field in the database.
  update_post_meta($post_id, '_bad_weather_program', $active );
}

function stf2015_filter_map_config($config) {
  $active = get_post_meta($config['id'], '_bad_weather_program', true);
  $config['_bad_weather_program'] = ($active == '1') ? true : false;
  return $config;
}

add_filter('makae_gm_menu_content', 'stf2015_hooks_make_menu');
add_action('wp_enqueue_scripts', 'stf2015_enqueue_scripts');
add_action('admin_enqueue_scripts', 'stf2015_admin_enqueue_scripts');

// METABOX for Bad Weather
add_action('add_meta_boxes', 'stf2015_add_metabox');
add_action('save_post', 'stf2015_save_meta_box_data');

add_filter('makae-gm-map-config', 'stf2015_filter_map_config');