<?php

/**
* @file aae_blocks
*
* @function block_aae_print_letzte_events
* @function block_aae_print_letzte_events
* @function block_aae_count_projects_events
*/

/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Events.
 * Wird in theme/page--front.tpl.php aufgerufen.
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_events.php';
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

  /*   ->fields('a', array(
      'name',
      'EID',
    )) */

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
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_akteure.php';
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

 foreach($letzteakteure as $row){
    $resultakteure[] = $row; //array('AID' => $row->AID, 'name' => $row->name);
  }

  return $resultakteure;
}

/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Projekte.
 * Wird in theme/page--front.tpl.php aufgerufen
 *
 * @use Einzubinden via require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/block_aae_letzte_akteure.module';
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

?>
