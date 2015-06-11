<?php
define('STF2015_LOCATION_CONFIG', serialize(array(
  'sportshall' => array(
    'label' => __('Turnhalle', 'mgm-stf2015'),
    'mapid' => 'turnfest',
    'coords' => array(
      'latitude' => '47.140256',
      'longitude' => '7.367363'
    )
  ),
  'marathon' => array(
    'label' => __('Start Cross-Lauf', 'mgm-stf2015'),
    'mapid' => 'turnfest',
    'coords' => array(
      'latitude' => '47.145642',
      'longitude' => '7.358565'
    )
  ),
  'party' => array(
    'label' => __('Festgelände', 'mgm-stf2015'),
    'mapid' => 'turnfest',
    'coords' => array(
      'latitude' => '47.143263',
      'longitude' => '7.357492'
    )
  )
)));

define('STF2015_JS_CONFIG', serialize(array(
  'bad_weather_text' => 'Diese Disziplin findet in der Turnhalle statt',
  'bad_weather_active_default' => false,
  'default_key' => '__DEFAULT__'
)));