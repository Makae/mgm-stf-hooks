<?php
define('STF2015_LOCATION_CONFIG', serialize(array(
  'sportshall' => array(
    'label' => __('Turnhalle', 'mgm-stf2015'),
    'mapid' => '246',
    'coords' => array(
      'latitude' => '47.1441',
      'longitude' => '7.357'
    )
  ),
  'marathon' => array(
    'label' => __('Start Cross-Lauf', 'mgm-stf2015'),
    'mapid' => '245',
    'coords' => array(
      'latitude' => '47.147422',
      'longitude' => '7.35878'
    )
  ),
  'party' => array(
    'label' => __('Festgelände', 'mgm-stf2015'),
    'mapid' => '245',
    'coords' => array(
      'latitude' => '47.1441',
      'longitude' => '7.357'
    )
  )
)));

define('STF2015_JS_CONFIG', serialize(array(
  'bad_weather_text' => 'Diese Disziplin findet in der Turnhalle statt',
  'bad_weather_active_default' => false,
  'default_key' => '__DEFAULT__'
)));