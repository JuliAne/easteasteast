<?php

/**
* @file aae_blocks
*
* @function block_aae_print_letzte_events
* @function block_aae_print_letzte_events
* @function block_aae_count_projects_events
* @function block_aae_print_letzte_artikel
*/

/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Events.
 * Wird in theme/page--front.tpl.php aufgerufen.
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
 */

function block_aae_print_letzte_events($limit = 3) {

  $tbl_event = "aae_data_event";

  require_once 'database/db_connect.php';
  $db = new DB_CONNECT();

  $letzteEvents = db_select($tbl_event, 'a')
    ->fields('a')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  // TODO Fields * durch Werte ersetzen

  $resultEvents = array();

 foreach($letzteEvents as $row){
    $resultEvents[] = $row;
  }

  return $resultEvents;
}


/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Projekte.
 * Wird in theme/page--front.tpl.php aufgerufen
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
 */

function block_aae_print_letzte_akteure($limit = 3) {

  $tbl_akteur = "aae_data_akteur";

  require_once 'database/db_connect.php';
  $db = new DB_CONNECT();

  $letzteakteure = db_select($tbl_akteur, 'a')
    ->fields('a')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  $resultakteure = array();

  // TODO Fields * durch Werte ersetzen

 foreach($letzteakteure as $row){
    $resultakteure[] = $row; //array('AID' => $row->AID, 'name' => $row->name);
  }

  return $resultakteure;
}

/**
 * Kleiner, interner(!) Block zum Aufzählen ("count") aller eingetragenen
 * Projekte und Events. Wird im Slider in theme/page--front.tpl.php aufgerufen.
 *
 * UNVOLLSTÄNDIG!
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
 */

function block_aae_count_projects_events() {

/*  $tbl_akteur = "aae_data_akteur";

  require_once 'database/db_connect.php';
  $db = new DB_CONNECT();

  $letzteakteure = db_select($tbl_akteur, 'a')
    ->fields('a')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  $resultakteure = array();

 foreach($letzteakteure as $row){
    $resultakteure[] = $row; //array('AID' => $row->AID, 'name' => $row->name);
  } */

  $resultadresse = db_select($tbl_adresse, 'b')
    ->fields('b', array(
      'ADID',
    ))
    ->condition('strasse', $strasse, '=')
    ->condition('nr', $nr, '=')
    ->condition('plz', $plz, '=')
    ->condition('ort', $ort, '=')
    ->execute();
  $count = $resultadresse->rowCount();
  $adid = "";

  return $myCounts;

  // return array('akteure' => xy, 'projekte' => xy):
}

/**
 * Kleiner, interner(!) Block zur Ausgabe der letzten xy Blog ("Journal")-Artikel
 *
 * Wird in theme/page--front.tpl.php aufgerufen
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
 */

function block_aae_print_letzte_artikel($limit = 3) {


    $result = db_select('node', 'n')
      ->fields('n')
      ->condition('type', 'article')
      ->condition('status', 1)
      ->orderBy('created', 'DESC')
      ->range(0, $limit)
      ->execute()
      ->fetchAll();

  /*  if ($node_title_list = node_title_list($result)) {
      $block['subject'] = t('Recent blog posts');
      $block['content']['blog_list'] = $node_title_list;
      $block['content']['blog_more'] = array(
        '#theme' => 'more_link',
        '#url' => 'blog',
        '#title' => t('Read the latest blog entries.'),
      ); */

      return $result;
}

?>
