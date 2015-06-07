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

function stf2015_enqueue_scripts() {
  wp_enqueue_style('stf2015', plugin_dir_url(__FILE__) . 'css/mgm-stf.css');
  wp_enqueue_script('stf2015', plugin_dir_url(__FILE__) . 'js/mgm-stf2015.js', array('makae-gm_init'), 1, true);
}

add_filter('makae_gm_menu_content', 'stf2015_hooks_make_menu');
add_action('wp_enqueue_scripts', 'stf2015_enqueue_scripts');