<?php

function aae_preprocess_html(&$variables) {

  drupal_add_js(path_to_theme().'/js/jquery-1.11.3.min.js'); // Wenn Du das siehst, check mal, ob's ne neue Version gibt

  drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js');
  drupal_add_js(path_to_theme().'/js/pace.min.js');
  drupal_add_js(path_to_theme().'/js/doubletaptogo.min.js'); // Mobile-Menü
  drupal_add_js(path_to_theme().'/js/app.js');

  drupal_add_css(path_to_theme().'/css/foundation.min.css');
  drupal_add_css('https://fonts.googleapis.com/css?family=Open+Sans:400,300', array('type' => 'external')); // TODO: Auslagern in local-repo
  drupal_add_css(path_to_theme().'/css/pace.css');
  drupal_add_css(path_to_theme().'/css/app.css');

  /* Checke Seitentyp, hänge entsprechendes CSS/JS an den Header: */

  $path = explode("/", current_path()); // Welcome URL-Injection :D TODO: Call ClearContent()

  switch(trim($path[0])) {

  case ('node') :

    if (drupal_is_front_page()) {

     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.css', array('type' => 'external'));
     drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/MarkerCluster.Default.css', array('type' => 'external'));
     drupal_add_css(path_to_theme().'/css/jquery.fullPage.css');

     drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
     drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-markercluster/v0.4.0/leaflet.markercluster.js');
     drupal_add_js(path_to_theme().'/js/home.js');

   } else {
    // Node--article.tpl.php
    drupal_add_css(path_to_theme(). '/css/article.css');
   }

  break;

  case ('Akteurprofil') :

    drupal_add_css(path_to_theme().'/css/project.css');
    drupal_add_css('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.css', array('type' => 'external'));
    drupal_add_js('https://api.tiles.mapbox.com/mapbox.js/v2.1.9/mapbox.js');
    drupal_add_js('L.mapbox.accessToken = "pk.eyJ1IjoibWF0emVsb3QiLCJhIjoiM3JrY3dQTSJ9.IGSonCNVbK5UzSYoxrgMjg";', 'inline');

  break;

  case ('Akteurformular'):
  case ('Akteuredit') :
  case ('Eventformular') :
  case ('Eventedit') :

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_css(path_to_theme().'/css/default.css');
   drupal_add_js(path_to_theme().'/js/zebra_datepicker.js');
   drupal_add_js('https://cdn.ckeditor.com/4.4.3/full-all/ckeditor.js');
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.css');
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.editor.css');
   drupal_add_js(path_to_theme().'/js/editform.js');

  break;

  case ('Akteure') :

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   // drupal_add_js(path_to_theme().'/js/stalactite.min.js');
   //drupal_add_js('$("#block-system-main #akteure").stalactite();', array('type' => 'inline', 'scope' => 'footer'));

  break;

  default:

    drupal_add_css(path_to_theme(). '/css/subpage.css');

  break;

 }

  global $user;

  if (array_intersect(array('redakteur','administrator'), $user->roles)) {
   echo '<!-- IF IS_ADMIN --><style type="text/css">#mainnav { top: 65px !important; }</style><!-- /IF -->';
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
    $form['actions']['submit']['#value'] = 'Abschicken';

  }
}
?>
