<?php

/**
* @file aae_blocks.php
*
* Ein paar Hilfsfunktion für das Theme, v.a. Startseite...
* @use require_once DRUPAL_ROOT . '/sites/all/modules/aae_data/aae_blocks.php';
*
* @function print_next_events
* @function print_letzte_akteure
* @function count_projects_events
* @function print_my_akteure
*/

namespace Drupal\AaeData;

Class aae_blocks extends aae_data_helper {

/**
 * Wird in templates/page--front.tpl.php aufgerufen.
 */
public function print_next_events($limit = 6) {

 require_once('models/events.php');
 $this->events = new events();

 // Show furthest of latest 6 events first
 $events = $this->events->getEvents(array(
  'limit' => $limit,
  'start' => array(
     '0' => array(
      'date' => (new \DateTime(date()))->format('Y-m-d 00:00:00'),
      'operator' => '>='
     )
    )
 ), 'normal', false, 'ASC');

  return $events;
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
    ->fields('ad', array('bezirk'))
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
