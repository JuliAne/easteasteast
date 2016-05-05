<?php

namespace Drupal\AaeData;

Class events extends aae_data_helper {

  //$tbl_event
  var $event_id = "";
  var $name = "";
  var $veranstalter = "";
  var $start = "";
  var $ende = "";
  var $zeit_von = "";
  var $zeit_bis = "";
  var $hat_zeit_von = true;
  var $hat_zeit_bis = true;
  var $bild = "";
  var $kurzbeschreibung = "";
  var $url = "";
  var $created = "";
  var $modified = "";

 /*
  ???public function getSingleEvent
  public function getEvents
  public function setEvents
  private function __addEventChildren($startQuery, $endQuery)
 */

 /*private $allowed_selectors = array('adresse','akteur','tags');

 public function getEvents($selectors = NULL, $options = array()){


  if (is_array($selectors) && !empty($selectors)) {

   if (isset($selectors['adresse']))
   }
  }

 }

 public function setEvent($event_id) {

 }

 public function getEventAdresse($event_id) {

 }*/

 /*
  * function getEvents($selectors[array], $fields[array])
  * @return Event-objects
  */
 public function getEvents($selectors = NULL, $fields = NULL) {

   $fields['parent_EID'] = NULL;

   $events = db_select($this->tbl_event, 'e')
    ->fields('e'); // TODO

   foreach ($selectors as $key => $selector) {

    if ($key == 'start_ts') {
     $events->condition($key, $selector, '>=');
    } else if ($key == 'ende_ts') {
     $events->condition($key, $selector, '<=');
     $events->condition($key, '1000-01-01 00:00:00', '!=');
    } else {
     $events->condition($key, $selector, '=');
    }

   }

   $events->orderBy('start_ts', 'ASC');
   $resultEvents = $events->execute()->fetchAll();


   // Add specific data from other tables... we don't need no joins, yah'
   $counter = 0;
   foreach ($resultEvents as $event) {

    if (!empty($event->parent_EID)){
     $parentData = db_select($this->tbl_event,'e')->fields('e')->condition('EID', $event->parent_EID);
     /*$resultEvents[$counter] = (isset($resultEvents[$event->parent_EID]) && !empty($resultEvents[$event->parent_EID]))
     ? $resultEvents[$event->parent_EID]
     : $parentData->execute()->fetchAll(); */
     $resultEvents[$counter] = $parentData->execute()->fetchAssoc();
     $event->EID = $event->parent_EID;
    }

     //Selektion der Tags
     /*$resultSparten = db_select($this->tbl_event_sparte, 's')
      ->fields('s', array( 'hat_KID' ))
      ->condition('hat_EID', $event->EID, '=')
      ->execute();

     $countSparten = $resultSparten->rowCount();
     $sparten = array();

     if ($countSparten != 0) {

      foreach ($resultSparten as $row) {
       $resultSpartenName = db_select($this->tbl_sparte, 'sp')
       ->fields('sp')
       ->condition('KID', $row->hat_KID, '=')
       ->execute();

       foreach ($resultSpartenName as $row1) {
        $sparten[] = $row1;
       }
      }
     }

     $akteurId = db_select($this->tbl_akteur_events, 'ae')
      ->fields('ae', array('AID'))
      ->condition('EID', $event->EID, '=')
      ->execute()
      ->fetchObject();

     $resultAkteur = db_select($this->tbl_akteur, 'a')
      ->fields('a',array('AID','name','bild'))
      ->condition('AID', $akteurId->AID)
      ->execute()
      ->fetchAll(); */

    // Hack: add variables to $resultEvents-object
    $resultEvents[$counter] = (array)$resultEvents[$counter];
    #$resultEvents[$counter]['tags'] = $sparten;
    #$resultEvents[$counter]['akteur'] = $resultAkteur;
    $resultEvents[$counter]['start'] = new \DateTime($event->start_ts);
    $resultEvents[$counter]['ende'] = new  \DateTime($event->ende_ts);
    $resultEvents[$counter]['eventRecurringType'] = $event->recurring_event_type;
    $resultEvents[$counter] = (object)$resultEvents[$counter];

    $counter++;

  }

  return $resultEvents;

 }

  public function getTags($eid = null){

   $tags = db_query('SELECT s.KID, s.kategorie FROM {aae_data_sparte} s JOIN {aae_data_event_hat_sparte} ehs WHERE s.KID = ehs.hat_KID ORDER BY s.kategorie DESC');
   return $tags->fetchAll();

  }

  private function __addEventChildren($startQuery, $endQuery){

   $datePeriod = NULL;

   switch ($this->eventRecurringType) {
     case '2' :
      $datePeriod = 'P1W';
     break;
     case '3' :
      $datePeriod = 'P2W';
     break;
     case '4' :
      $datePeriod = 'P1M';
     break;
     case '5' :
      $datePeriod = 'P2M';
     break;
   }

  for ($i = 0; $i < 5; $i++) {

   $start = new \DateTime($startQuery);
   $start->add(new \DateInterval($datePeriod));
   $startQuery = $start->format('Y-m-d H:i:s');

   if ($endeQuery != '1000-01-01 00:00:00') {
    $ende = new \DateTime($endeQuery);
    $ende->add(new \DateInterval($datePeriod));
    $endeQuery = $ende->format('Y-m-d H:i:s');
   }

   $recurringEvent = db_insert($this->tbl_event)
    ->fields(array(
    'start_ts' => $startQuery,
    'ende_ts' => $endeQuery,
		'parent_EID' => $this->event_id,
    'recurring_event_type' => $this->eventRecurringType
   ))
   ->execute();

   }
  } // END __addEventChildren()


}
