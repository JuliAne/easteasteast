<?php

/**
* @file aae_blocks.php
*
* Ein paar Hilfsfunktion für das Theme...
* @use require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
*      aae_blocks::FUNKTION
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

  $letzteEvents = db_select($this->tbl_event, 'a')
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
 */

public function print_letzte_akteure($limit = 3) {

  $letzteakteure = db_select($this->tbl_akteur, 'a')
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
