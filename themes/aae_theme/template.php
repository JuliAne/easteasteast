<?php

function aae_preprocess_html(&$variables) {

  $viewport = array(
   '#tag' => 'meta',
   '#attributes' => array(
     'name' => 'viewport',
     'content' => 'width=device-width, initial-scale=1, maximum-scale=1',
   ),
  );
  drupal_add_html_head($viewport, 'viewport');

  drupal_add_js(path_to_theme().'/js/jquery-1.11.3.min.js');

  drupal_add_js(path_to_theme().'/js/pace.min.js');
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

     drupal_add_css(path_to_theme().'/css/jquery.fullPage.css');
     drupal_add_js(path_to_theme().'/js/jquery.fullPage.min.js');

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
  case ('eventprofil') :
    drupal_add_css(path_to_theme(). '/css/subpage.css');
    drupal_add_css(path_to_theme().'/css/project.css');

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
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.css');
   drupal_add_css(base_path().'sites/all/modules/ckeditor/css/ckeditor.editor.css');
   drupal_add_js(path_to_theme().'/js/editform.js');

  break;

  case ('events') :

   $js = '$(window).ready(function(){$("#eventSpartenInput").tokenize({displayDropdownOnFocus:true,newElements:false,placeholder:"z.B. Jazz, Kultur,..."});});';
   $js .= '$(window).ready(function(){$("#eventBezirkInput").tokenize({displayDropdownOnFocus:true,newElements:false,placeholder:"z.B. Reudnitz..."});});';

   drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_js($js, 'inline');
   drupal_add_js(path_to_theme().'/js/foundation.min.js');
   drupal_add_js('$(document).foundation();', array('type'=>'inline','scope'=>'footer'));

  break;

  case ('akteure') :

   $js = '$(window).ready(function(){$(".tokenize").tokenize({displayDropdownOnFocus:true,newElements:false});});';

     drupal_add_css(path_to_theme(). '/css/subpage.css');
   drupal_add_css(path_to_theme().'/css/jquery.tokenize.css');
   drupal_add_js(path_to_theme().'/js/jquery.tokenize.js');
   drupal_add_js($js, 'inline');
   //drupal_add_js(path_to_theme().'/js/wookmark.min.js');
   //drupal_add_js('$("#akteure").wookmark({offset:10});', array('type' => 'inline', 'scope' => 'footer'));

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

    $form['actions']['submit']['#attributes']['class'][] = 'medium button';
    $form['actions']['submit']['#value'] = 'Einloggen';
    $form['name']['#description'] = '';
    $form['pass']['#description'] = '';

    $form['warning']['#description'] = 'd';

  } else  { // @TODO ELSEIF SEARCH-FORM

    $form['actions']['submit']['#attributes']['class'][] = 'small button';
    $form['actions']['submit']['#value'] = 'Absenden';
    $form['actions']['preview']['#attributes']['class'][] = 'small button secondary';


  }
 }

 function aae_css_alter(&$css) {

 // Remove Drupal core css

 global $user;

 if (!array_intersect(array('redakteur','administrator'), $user->roles)) {

 $exclude = array(
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
