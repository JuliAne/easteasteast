<?php

function aae_preprocess_html(&$variables) {

 // Add theme-specific meta-tags to HTML-head
  $html_heads = array(
   'viewport' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'viewport',
     'content' => 'width=device-width, initial-scale=1, maximum-scale=1',
    )
   ),
   'theme-color' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'theme-color',
     'content' => '#2199E8',
    )
   ),
   'manifest' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'manifest',
     'content' => base_path().path_to_theme().'/icons/manifest.json',
    )
   ),
   'icon' => array(
    '#tag' => 'link',
    '#attributes' => array(
     'rel' => 'icon',
     'type' => 'image/png',
     'sizes' => '32x32',
     'href' => base_path().path_to_theme().'/icons/favicon-32x32.png',
    )
   ),
   'apple-touch-icon' => array(
    '#tag' => 'link',
    '#attributes' => array(
     'rel' => 'apple-touch-icon',
     'sizes' => '60x60',
     'href' => base_path().path_to_theme().'/icons/apple-icon-60x60.png',
    )
   ),
   'msapplication-navbutton-color' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'msapplication-navbutton-color',
     'content' => '#2199E8',
    )
   ),
   'msapplication-TileColor' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'msapplication-TileColor',
     'content' => '#2199E8',
    )
   ),
   'msapplication-TileImage' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'msapplication-TileImage',
     'content' => base_path().path_to_theme().'/icons/ms-icon-144x144.png',
    )
   ),
   'description' => array(
    '#tag' => 'meta',
    '#attributes' => array(
     'name' => 'description',
     'content' => t('Deine Stadtteilplattform für den Leipziger Osten: Lerne Akteure & Events aus Deinem Bezirk kennen.'),
    )
   )
  );

  foreach ($html_heads as $key => $data) {
    drupal_add_html_head($data, $key);
  }

  drupal_add_feed(base_path().'rss.xml', t('Stadtteiljournal'));
  drupal_add_feed(base_path().'events/rss', t('Alle kommenden Veranstaltungen'));

  drupal_add_js(path_to_theme().'/js/jquery-1.11.3.min.js', array('scope'=>'header'));

  drupal_add_js(path_to_theme().'/js/pace.min.js');
  /* Would be better if it looked like <script data-pace-options="{'ajax':false}" src=".."></script> */
  drupal_add_js(path_to_theme().'/js/app.js', array('scope'=>'footer'));

  drupal_add_css(path_to_theme().'/css/foundation.min.css');
  drupal_add_css(path_to_theme().'/css/app.css');

  drupal_add_js(path_to_theme().'/js/foundation.min.js', array('scope'=>'footer'));
  drupal_add_js('$(document).foundation();', array('type'=>'inline','scope'=>'footer'));

  /* Checke Seitentyp, hänge entsprechendes CSS/JS an den Header: */

  $path = explode("/", current_path()); // TODO: Call filter_xss()

  switch (strtolower(trim($path[0]))) {

  case ('node') :

    $node = node_load(arg(1));

    if (drupal_is_front_page()) {

     drupal_add_css(path_to_theme().'/css/page_front.css');
     drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js', array('scope'=>'footer'));
     drupal_add_js(path_to_theme().'/js/home.js', array('scope' => 'footer'));

   } else if ($node->type == 'article') {
    // Node--article.tpl.php
    drupal_add_css(path_to_theme(). '/css/subpage.css');
    drupal_add_css(path_to_theme(). '/css/article.css');
   } else {
    drupal_add_css(path_to_theme(). '/css/subpage.css');
   }

  break;

  case ('user') :

    drupal_add_css(path_to_theme(). '/css/subpage.css');
    drupal_add_css(path_to_theme().'/css/user.css');

  break;

  case ('akteurprofil') :
  case ('eventprofil') :
    drupal_add_css(path_to_theme(). '/css/subpage.css');
    drupal_add_css(path_to_theme().'/css/project.css');

    if (trim($path[2]) == 'edit')
     addAkteurEventsAddEditHead();

  break;

  case ('events') :

   drupal_add_css(path_to_theme(). '/css/subpage.css');

   if (trim($path[1]) == 'new') {

    addAkteurEventsAddEditHead();

   } else {

    $js = '$(window).ready(function(){$("#eventSpartenInput").tokenize({displayDropdownOnFocus:true,newElements:false,placeholder:"z.B. Jazz, Kultur,..."});});';
    $js .= '$(window).ready(function(){$("#eventBezirkInput").tokenize({displayDropdownOnFocus:true,newElements:false,placeholder:"z.B. Reudnitz..."});});';

    drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
    drupal_add_js(path_to_theme().'/js/jquery.tokenize.js', array('scope'=>'footer'));
    drupal_add_js($js, array('type' => 'inline', 'scope' => 'footer'));

   }

  break;

  case ('akteure') :

   drupal_add_css(path_to_theme(). '/css/subpage.css');

   if (trim($path[1]) == 'new') {

    addAkteurEventsAddEditHead();

   } else {

    $js = '$(window).ready(function(){$(".tokenize").tokenize({displayDropdownOnFocus:true,newElements:false});$("#akteure-content").stalactite({cssPrep:false,loader:"",duration:50});});';

    drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
    drupal_add_js(path_to_theme().'/js/jquery.tokenize.js', array('scope'=>'footer'));
    drupal_add_js(path_to_theme().'/js/stalactite.min.js', array('scope'=>'footer'));
    drupal_add_js($js, 'inline');

   }

  break;
  
  case ('festivals') :
    addAkteurEventsAddEditHead();
  break;

  default:
    drupal_add_css(path_to_theme(). '/css/subpage.css');

  break;

 }

  global $user;

  if (array_intersect(array('festival','redakteur','administrator'), $user->roles)) {
   echo '<!-- IF IS_ADMIN --><style type="text/css">#mainnav{top: 65px !important;}#singlesite{margin-top:130px;}.aaeActionBar{margin-top:-230px !important;margin-bottom:160px !important;}</style><!-- /IF -->';
  }
} // END function aae_preprocess_html

// Helper function for aae-forms (../add & ../edit)
function addAkteurEventsAddEditHead(){
  drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
  drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
  drupal_add_css(path_to_theme().'/css/default.css');
  drupal_add_js(path_to_theme().'/js/zebra_datepicker.js');
  drupal_add_js('https://cdn.ckeditor.com/4.5.7/basic/ckeditor.js');
  drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.css');
  drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.editor.css');
  drupal_add_js(path_to_theme().'/js/editform.js');
}


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

    $form['name']['#attributes']['placeholder'] = t('Benutzername / E-Mail');
    $form['pass']['#attributes']['placeholder'] = t('Passwort');
    $form['name']['#title_display'] = "invisible";
    $form['pass']['#title_display'] = "invisible";

    $form['links']['#markup'] = '';

    $form['actions']['submit']['#attributes']['class'][] = 'large-12 columns medium button';
    $form['actions']['submit']['#value'] = t('Anmelden');
    $form['name']['#description'] = '';
    $form['pass']['#description'] = '';

  } else if ( TRUE === in_array( $form_id, array('search_block', 'user_block_form')) )  {

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = t('Suchen');

  } else {

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = t('Absenden');
    $form['actions']['preview']['#attributes']['class'][] = 'small button secondary';

  }
 }

 function aae_css_alter(&$css) {

 // Remove Drupal core css

 global $user;

 if (!array_intersect(array('redakteur','administrator'), $user->roles)) {

 $exclude = array(
 'sites/all/modules/invite/modules/invite_by_email/css/invite_by_email.css' => FALSE,
 'modules/aggregator/aggregator.css' => FALSE,
 'modules/block/block.css' => FALSE,
 'modules/book/book.css' => FALSE,
 'modules/comment/comment.css' => FALSE,
 'modules/dblog/dblog.css' => FALSE,
 'modules/field/theme/field.css' => FALSE,
 'modules/file/file.css' => FALSE,
 'modules/filter/filter.css' => FALSE,
 'modules/forum/forum.css' => FALSE,
 'modules/help/help.css' => FALSE,
 'modules/menu/menu.css' => FALSE,
 'modules/node/node.css' => FALSE,
 'modules/openid/openid.css' => FALSE,
 'modules/poll/poll.css' => FALSE,
 'modules/profile/profile.css' => FALSE,
 'modules/search/search.css' => FALSE,
 'modules/statistics/statistics.css' => FALSE,
 'modules/syslog/syslog.css' => FALSE,
 'modules/system/admin.css' => FALSE,
 'modules/system/maintenance.css' => FALSE,
 'modules/system/system.css' => FALSE,
 'modules/system/system.admin.css' => FALSE,
 'modules/system/system.base.css' => FALSE,
 'modules/system/system.maintenance.css' => FALSE,
 'modules/system/system.messages.css' => FALSE,
 'modules/system/system.menus.css' => FALSE,
 'modules/system/system.theme.css' => FALSE,
 'modules/taxonomy/taxonomy.css' => FALSE,
 'modules/tracker/tracker.css' => FALSE,
 'modules/update/update.css' => FALSE,
 'modules/user/user.css' => FALSE,
 'misc/vertical-tabs.css' => FALSE,

 // Remove contrib module CSS
 drupal_get_path('module', 'views') . '/css/views.css' => FALSE, );
 $css = array_diff_key($css, $exclude);

 }
}
?>
