<?php

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
  $html = '';
  $html .=
    '<div class="mgm-stf-position-wrapper">' .
      '<h5>' . __('Position', 'mgm-stf2015') . '</h5>' .
      '<ul class="mgm-stf-menu-position stf2015-map-menu">' .
        '<li class="stf2015-target toggle"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Where am I?', 'mgm-stf2015') . '</span></li>' .
      '</ul>' .
    '</div>';
  $html .=
    '<h5>' . __('Map display', 'mgm-stf2015') . '</h5>' .
    '<ul class="mgm-stf-display-option-list stf2015-map-menu">' .
      '<li class="option stf2015-overlay active" data-map-type-key="ROADMAP" data-map-overlay="true"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Overlay', 'mgm-stf2015') . '</span></li>' .
      '<li class="option stf2015-map" data-map-type-key="ROADMAP" data-map-overlay="false"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Map', 'mgm-stf2015') . '</span></li>' .
      '<li class="option stf2015-satellite" data-map-type-key="SATELLITE" data-map-overlay="false"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Satellite', 'mgm-stf2015') . '</span></li>' .
    '</ul>';

  $html .=
    '<h5>' . __('Locations', 'mgm-stf2015') . '</h5>' .
    '<ul class="mgm-stf-menu-links stf2015-map-menu">' .
      '<li class="stf2015-sportshall"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Sports hall', 'mgm-stf2015') . '</span></li>' .
      '<li class="stf2015-marathon"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Marathon', 'mgm-stf2015') . '</span></li>' .
      '<li class="stf2015-party"><span class="map-menu-icon"></span><span class="map-menu-label">' . __('Party', 'mgm-stf2015') . '</span></li>' .
    '</ul>';

  return $html;
}

function stf2015_enqueue_scripts() {
  wp_enqueue_style('stf2015', plugin_dir_url(__FILE__) . 'css/mgm-stf.css');
  wp_enqueue_script('stf2015', plugin_dir_url(__FILE__) . 'js/mgm-stf2015.js', array('makae-gm_init'), 1, true);
}

add_filter('makae_gm_menu_content', 'stf2015_hooks_make_menu');
add_action('wp_enqueue_scripts', 'stf2015_enqueue_scripts');