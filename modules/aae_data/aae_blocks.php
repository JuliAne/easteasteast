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
*/


$modulePath = drupal_get_path('module', 'aae_data');
include_once $modulePath . '/aae_data_helper.php';


Class aae_blocks extends aae_data_helper {

/**
 * Kleiner, interner(!) Block zum Anzeigen der letzten drei (=$limit) eingetragenen Events.
 * Wird in templates/page--front.tpl.php aufgerufen.
 */

public function print_letzte_events($limit = 6) {

 //Show furthest of latest 6 events first
 $events = db_select($this->tbl_event, 'a')
  ->fields('a')
  ->orderBy('created', 'DESC')
  ->range(0, $limit);

  $resultEvents = $events->execute()->fetchAll();

  foreach ($resultEvents as $counter => $event){

   // Hack: add variable to $resultEvents-object
   $resultEvents[$counter] = (array)$resultEvents[$counter];
   $resultEvents[$counter]['start'] = new DateTime($event->start_ts);
   $resultEvents[$counter]['ende'] = new DateTime($event->ende_ts);
   $resultEvents[$counter] = (object)$resultEvents[$counter];

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
    ->orderBy('created', 'DESC')
    ->range(0, $limit)
    ->execute()
    ->fetchAll();

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

  $renderSmallName = false;
  $akName = explode(" ", $akteur->name);

  foreach ($akName as $name) {
   if (strlen($name) >= 17 || strlen($akteur->name) >= 30) $renderSmallName = true;
  }

   // Hack: add variable to $resultAkteure-object
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['bezirk'] = $bezirk['bezirksname'];
   $resultAkteure[$counter]['renderSmallName'] = $renderSmallName;
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
} // end class aae_block

?>
