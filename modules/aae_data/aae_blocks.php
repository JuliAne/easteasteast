<?php

/**
* @file aae_blocks.php
*
* Ein paar Hilfsfunktion für das Theme...
* @use require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
*
* @function print_letzte_events
* @function print_letzte_akteure
* @function count_projects_events
* @function print_my_akteure
* @function tagcloud
*/

$modulePath = drupal_get_path('module', 'aae_data');
include_once $modulePath . '/aae_data_helper.php';

Class aae_blocks extends aae_data_helper {

/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Events.
 * Wird in templates/page--front.tpl.php aufgerufen.
 */

public function print_letzte_events($limit = 6) {

//Show nearest of latest 6 events first
// $sql = '(SELECT * FROM "aae_data_event" ORDER BY "EID" DESC LIMIT 6) ORDER BY "start" ASC';
// $letzteEvents = db_query($sql)->fetchAll();

//Show furthest of latest 6 events first
  $letzteEvents = db_select($this->tbl_event, 'a')
    ->fields('a')
    ->orderBy('EID','DESC')
    ->range(0,$limit)
    ->execute()
    ->fetchAll();

  $resultEvents = array();

  foreach($letzteEvents as $row){
    $resultEvents[] = $row;
  }

  return $resultEvents;
}


/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten vier (=$limit) eingetragenen Projekte.
 * Wird in theme/page--front.tpl.php aufgerufen
 */

public function print_letzte_akteure($limit = 4) {

  $resultAkteure = db_select($this->tbl_akteur, 'a')
    ->fields('a')
    ->orderBy('AID', 'DESC')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

  /*foreach($letzteAkteure as $row){
    $resultakteure[] = $row; //array('AID' => $row->AID, 'name' => $row->name);
  } */

  // Get Bezirk
  foreach ($resultAkteure as $counter => $akteur) {

   $adresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad', array('bezirk','gps'))
    ->condition('ADID', $akteur->adresse, '=')
    ->execute()
    ->fetchAssoc();

   $bezirk = db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->condition('BID', $adresse['bezirk'], '=')
    ->execute()
    ->fetchAssoc();

   // Hack: add variable to $resultAkteure-object
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['bezirk'] = $bezirk['bezirksname'];
   $resultAkteure[$counter] = (object)$resultAkteure[$counter];

 }
 return $resultAkteure;
}

/**
 * Kleiner, interner(!) Block zum Aufzählen ("count") aller eingetragenen
 * Projekte und Events. Wird im Slider in theme/page--front.tpl.php aufgerufen.
 */

public function count_projects_events() {

  $count = array();

  $countAkteure = db_select($this->tbl_akteur, 'a')
  ->fields('a', array('AID'))
  ->execute();

  $countEvents = db_select($this->tbl_event, 'e')
  ->fields('e', array('EID'))
  ->execute();

  $count['akteure'] = $countAkteure->rowCount();
  $count['events'] = $countEvents->rowCount();

  return $count;

}

/**
 * Kleiner, interner(!) Block zum Anzeigen aller mit dem eigenen Account verknüften Akteure.
 * Wird in theme/header.tpl.php aufgerufen.
 */

 public function print_my_akteure() {

  $results = array();

  global $user;

  $myAkteure = db_select($this->tbl_hat_user, 'ha')
  ->fields('ha')
  ->condition('hat_UID', $user->uid, '=')
  ->execute()
  ->fetchAll();

  foreach ($myAkteure as $akteur) {

    // We don't need no Join's, masafakkaaa...

    $results[] = db_select($this->tbl_akteur, 'a')
    ->fields('a', array('AID','name'))
    ->condition('AID', $akteur->hat_AID, '=')
    ->execute()
    ->fetchAll();
  }

  return $results;

 }

 /**
 * @function tagcloud()
 *
 * ...
 */

 public function tagcloud() {



 }
} // end class aae_block

?>
