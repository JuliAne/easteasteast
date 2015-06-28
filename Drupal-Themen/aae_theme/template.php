<?php

function aae_preprocess_html(&$variables) {
  drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
  drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
  drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));
  drupal_add_css('http://fonts.googleapis.com/css?family=Open+Sans:400,300', array('type' => 'external'));

  drupal_add_js('http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
  drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
  drupal_add_js(path_to_theme().'https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');
  drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js');
  drupal_add_js(path_to_theme().'/js/pace.min.js');
  drupal_add_js(path_to_theme().'/js/doubletaptogo.min.js');
  drupal_add_js(path_to_theme().'/js/app.js');
  drupal_add_js(path_to_theme().'/js/home.js');


  if (user_access('administer')) {
   echo '<style type="text/css">#mainnav { margin-top: 65px !important; }</style>';
 }
}

?>
