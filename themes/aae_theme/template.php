<?php

function aae_preprocess_html(&$variables) {

  drupal_add_js(path_to_theme().'/js/jquery-1.11.3.min.js'); // Wenn Du das siehst, check mal, ob's ne neue Version gibt

  drupal_add_js(path_to_theme().'/js/pace.min.js');
  drupal_add_js(path_to_theme().'/js/doubletaptogo.min.js'); // Mobile-Menü, bisher ungenutzt
  drupal_add_js(path_to_theme().'/js/app.js');

  drupal_add_css(path_to_theme().'/css/foundation.min.css');
  drupal_add_css('https://fonts.googleapis.com/css?family=Open+Sans:400,300', array('type' => 'external')); // TODO: Auslagern in local-repo
  drupal_add_css(path_to_theme().'/css/pace.css');
  drupal_add_css(path_to_theme().'/css/app.css');

  /* Checke Seitentyp, hänge entsprechendes CSS/JS an den Header: */

  $path = explode("/", current_path()); // TODO: Call ClearContent()

  switch(strtolower(trim($path[0]))) {

  case ('node') :

     $node = node_load(arg(1));

    if (drupal_is_front_page()) {

     /*drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));*/
     drupal_add_css(path_to_theme().'/css/jquery.fullPage.css');
     drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js');

     /*drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
     drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');*/
     drupal_add_js(path_to_theme().'/js/home.js');

   } else if ($node->type == 'article') {
    // Node--article.tpl.php
    drupal_add_css(path_to_theme(). '/css/subpage.css');
    drupal_add_css(path_to_theme(). '/css/article.css');
   } else {
    drupal_add_css(path_to_theme(). '/css/subpage.css');
   }

  break;

  case ('akteurprofil') :

    drupal_add_css(path_to_theme().'/css/project.css');
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js('L.mapbox.accessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";', 'inline');

  break;

  case ('akteurformular'):
  case ('akteuredit') :
  case ('eventformular') :
  case ('eventedit') :

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_css(path_to_theme().'/css/default.css');
   drupal_add_js(path_to_theme().'/js/zebra_datepicker.js');
   drupal_add_js('https://cdn.ckeditor.com/4.4.3/basic/ckeditor.js');
   // sites/all/modules/ckeditor/ckeditor.js
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.css');
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.editor.css');
   drupal_add_js(path_to_theme().'/js/editform.js');

  break;

  case ('events') :

   $js = '$(window).ready(function(){$(".tokenize").tokenize({displayDropdownOnFocus:true,newElements:false});});';

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_js($js, 'inline');

  break;

  case ('akteure') :

   $js = '$(window).ready(function(){$(".tokenize").tokenize({displayDropdownOnFocus:true,newElements:false});});';

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_js($js, 'inline');
   //drupal_add_js(path_to_theme().'/js/wookmark.min.js');
   //drupal_add_js('$("#akteure").wookmark({offset:10});', array('type' => 'inline', 'scope' => 'footer'));

   if (isset($_GET['submit']) && $_GET['presentation'] == 'map') {
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));

    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');
    drupal_add_js('L.mapbox.accessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";', 'inline');
    drupal_add_js(base_path().drupal_get_path('module', 'aae_data').'/LOdata.js');
    drupal_add_js('$(window).ready(function(){var map = L.mapbox.map("map", "matzelot.ke3420oc").setView([51.336, 12.433], 12);var markers = new L.MarkerClusterGroup({ showCoverageOnHover : false });

     for (var i = 0; i < addressPoints.length; i++) {
      var a = addressPoints[i];
      var title = a[2];
      var marker = L.marker(new L.LatLng(a[0], a[1]), {
          icon: L.mapbox.marker.icon({"marker-symbol": "pitch", "marker-color": "0044FF"}),
          title: title
      });

      marker.bindPopup(title);
      markers.addLayer(marker);
     }

     map.addLayer(markers);});', 'inline');
 }

  break;

  default:

    drupal_add_css(path_to_theme(). '/css/subpage.css');

  break;

 }

  global $user;

  if (array_intersect(array('redakteur','administrator'), $user->roles)) {
   echo '<!-- IF IS_ADMIN --><style type="text/css">#mainnav { top: 65px !important; }#content { margin-top: 30px; }</style><!-- /IF -->';
  }
} // END function aae_preprocess_html


/**
* Überschreibt das main-menu
*/

function aae_process_page(&$variables) {
    $menu_tree = menu_tree_all_data('main-menu');
    $variables['main_menu'] = menu_tree_output($menu_tree);
}

/**
* Überschreibt das Login-Form in header.tpl.php
*/

function aae_form_alter(&$form, &$form_state, $form_id) {

  if ( TRUE === in_array( $form_id, array('user_login', 'user_login_block')) ) {

    $form['name']['#attributes']['placeholder'] = t('Benutzername');
    $form['pass']['#attributes']['placeholder'] = t('Passwort');
    $form['name']['#title_display'] = "invisible";
    $form['pass']['#title_display'] = "invisible";

    $form['links']['#markup'] = '';

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = 'Einloggen';
    $form['name']['#description'] = t('');
    $form['pass']['#description'] = t('');

  } else  { // @TODO ELSEIF SEARCH-FORM

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = 'Absenden';

  }
}
?>
