<?php

namespace Drupal\AaeData;

/*
*  Small wannabe-model class that delivers methods
*  for getting and manipulating events-data.
*   
*  @use use \Drupal\AaeData\events()
*       $this->events = new events();
*/

Class events extends aae_data_helper {

  public function __construct() {
   parent::__construct();
  }

 /*TODO For API CLASS:
   private $allowed_selectors = array('adresse','akteur','tags'); */

 public function setEvent($data) {
  # TODO
 }

 /*
  * @return Event-object
  * @param $conditions : array
  * @param $fields : integer: MINIMAL output (= good for date-calculation),
  *                           NORMAL output (= full table-row),
  *                           COMPLETE (= joins all other tables)
  */
 public function getEvents($conditions = NULL, $fields = 'normal', $calledRecursively = false, $orderBy = 'ASC') {
   
   $events = db_select($this->tbl_event, 'e');

   if ($fields == 'minimal') {
    $events->fields('e', array('EID','name','start_ts','ende_ts','parent_EID','created','modified'));
   } else {
    $events->fields('e');
   }
   
   foreach ($conditions as $key => $condition) {

    switch ($key) {
      
     case ('start_ts') :
     case ('start') :
     
     // Make multiple start-times possible
     if (is_array($condition)) {
       
      foreach ($condition as $s) {
       $events->condition('start_ts', $s['date'], $s['operator']);
      }
      
     } else {
      $events->condition('start_ts', $condition, '>=');
     }
      break;
      
     case ('ende_ts') :
     case ('end') :
      $events->condition('ende_ts', $condition, '<=');
      $events->condition('ende', '1000-01-01 00:00:00', '!=');
      // TODO for API: Make various end-values possible
      break;
      
     case ('filter') :
      
      $events->condition('EID', $this->__filterEvents($conditions['filter']));
      /*    $or = db_or();

   foreach ($this->filteredEventIds as $event){
    $or->condition('EID', $event);
   }

   $rEvents->condition($or); */
     break;
     
     default : 
      $events->condition($key, $condition, '=');
      break;
    }

   }

   $events->orderBy('start_ts', $orderBy);
   $resultEvents = $events->execute()->fetchAll();

   // Add specific data from other tables...
   $counter = 0;

   foreach ($resultEvents as $event) {

    if (!empty($event->parent_EID) && !$calledRecursively){
     // Erbe vom Eltern-Element
     // TODO: Improve Query-performance
     $parentData = db_select($this->tbl_event,'e')->fields('e')->condition('EID', $event->parent_EID);
     /*$resultEvents[$counter] = (isset($resultEvents[$event->parent_EID]) && !empty($resultEvents[$event->parent_EID]))
     ? $resultEvents[$event->parent_EID]
     : $parentData->execute()->fetchAll(); */
     $resultEvents[$counter] = $parentData->execute()->fetchAssoc();
     $event->EID = $event->parent_EID;
    }

    // Hack: add variables to $resultEvents-object
    $resultEvents[$counter] = (array)$resultEvents[$counter];

    if ($fields == 'complete') {

     if ($event->recurring_event_type >= 1) {

      $childrenEvents = $this->getEvents(array('parent_EID' => $conditions['EID']), 'minimal', true);
      if (!empty($childrenEvents))
       $resultEvents[$counter]['childrenEvents'] = $childrenEvents;
      #???
     }

     $ersteller = db_select("users", 'u')
      ->fields('u', array('name'))
      ->condition('uid', $event->ersteller, '=')
      ->execute();

     $resultEvents[$counter]['ersteller'] = $ersteller->fetchObject();

     // Adresse + Bezirk - HATING DRUPAL JOINS IN PARTICULAR
     $resultAdresse = db_query('SELECT * FROM {aae_data_adresse} b INNER JOIN {aae_data_bezirke} bz ON bz.BID = b.bezirk WHERE b.ADID = :adresse', array(':adresse'=>$event->ort));
     $resultEvents[$counter]['adresse'] = $resultAdresse->fetchObject();

     // Tags
     $sparten = array();
     $sparten = $this->getTags($event->EID);

     $resultEvents[$counter]['tags'] = $sparten;

   } if ($fields == 'complete' || $fields == 'normal') {

     $akteurId = db_select($this->tbl_akteur_events, 'ae')
      ->fields('ae', array('AID'))
      ->condition('EID', $event->EID, '=')
      ->execute()
      ->fetchObject();

     $resultAkteur = db_select($this->tbl_akteur, 'a')
      ->fields('a',array('AID','name','bild'))
      ->condition('AID', $akteurId->AID)
      ->execute()
      ->fetchObject();
     // Could return full akteure->getAkteure(...)-object...

     $resultEvents[$counter]['akteur'] = $resultAkteur;

    }

    $resultEvents[$counter]['start'] = new \DateTime($event->start_ts);
    $resultEvents[$counter]['ende'] = new  \DateTime($event->ende_ts);
    $resultEvents[$counter]['created'] = new  \DateTime($event->created);
    $resultEvents[$counter]['modified'] = new  \DateTime($event->modified);
    $resultEvents[$counter]['eventRecurresTill'] = new \DateTime($event->event_recurres_till);
    $resultEvents[$counter]['eventRecurringType'] = $event->recurring_event_type;
    $resultEvents[$counter] = (object)$resultEvents[$counter];

    $counter++;

  }

  return $resultEvents;

 }

  public function getTags($eid = null){
   
   if (empty($eid)) {
    $tags = db_query('SELECT s.KID, s.kategorie FROM {aae_data_sparte} s JOIN {aae_data_event_hat_sparte} ehs WHERE s.KID = ehs.hat_KID ORDER BY s.kategorie DESC');
   } else {
    $tags = db_query('SELECT s.KID, s.kategorie FROM {aae_data_sparte} s JOIN {aae_data_event_hat_sparte} ehs WHERE s.KID = ehs.hat_KID AND ehs.hat_EID = :eid ORDER BY s.kategorie DESC', array(':eid'=>$eid));
   }
   return $tags->fetchAll();

  }

  public function addEventChildren($parent_EID, $eventRecurringType, $startQuery, $endQuery, $eventRecurresTill = null){
   
   $datePeriod = NULL;

   switch ($eventRecurringType) {
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
  
  // Generate max. 5 child-elements by default (or limit by eventRecurresTill)
  
  for ($i = 0; $i < 5; $i++) {

   $start = new \DateTime($startQuery);
   $start->add(new \DateInterval($datePeriod));
   $startQuery = $start->format('Y-m-d H:i:s');
   $doItFaggot = true;
   if (isset($eventRecurresTill) && $start->format('Ymd') > (new \DateTime($eventRecurresTill))->format('Ymd')){
    $doItFaggot = false;
   }
   
   if ($doItFaggot) {

    if ($endeQuery != '1000-01-01 00:00:00') {
     $ende = new \DateTime($endQuery);
     $ende->add(new \DateInterval($datePeriod));
     $endQuery = $ende->format('Y-m-d H:i:s');
    }

    $recurringEvent = db_insert($this->tbl_event)
     ->fields(array(
     'start_ts' => $startQuery,
     'ende_ts' => $endQuery,
		 'parent_EID' => $parent_EID,
     'recurring_event_type' => $eventRecurringType,
     'event_recurres_till' => '1000-01-01 00:00:00' # to be put out soon
    ))
    ->execute();
   
    } else {
     break;
    }

   }
  }

  public function removeEvent($event_id){

   $resultEvent = db_select($this->tbl_event, 'e')
    ->fields('e', array('bild','recurring_event_type'))
    ->condition('EID', $event_id, '=')
    ->execute()
    ->fetchObject();

   db_delete($this->tbl_akteur_events)
    ->condition('EID', $event_id, '=')
    ->execute();

   db_delete($this->tbl_event)
    ->condition('EID', $event_id, '=')
    ->execute();

   db_delete($this->tbl_event_sparte)
   ->condition('hat_EID', $event_id, '=')
   ->execute();

   // remove children-items, if given
   if (!empty($resultEvent->recurring_event_type)) {
    $this->removeEventChildren($event_id);
   }

   // remove profile-image
   $bild = end(explode('/', $resultEvent->bild));

   if (file_exists($this->short_bildpfad.$bild)) {
    @unlink($this->short_bildpfad.$bild);
   }

   menu_link_delete(NULL, 'eventprofil/'.$event_id);

  }

  public function removeEventChildren($eid) {
  
   $removeEventChildren = db_delete($this->tbl_event)
    ->condition('parent_EID', $eid)
    ->execute();

  }
  
  private function __filterEvents($filter){
    
   $filteredEventIds = array();
   $numFilter = 0;
   $filteredTags = array();
   $filteredBezirke = array();
   
   if (isset($filter['tags'])){
   
    $sparten = db_select($this->tbl_event_sparte, 'hs')
     ->fields('hs', array('hat_EID'));
    $and = db_and();
   
    foreach ($filter['tags'] as $tag) {
     $numFilters++;
     $tag = $this->clearContent($tag);
     $filteredTags[$tag] = $tag;
     $and->condition('hat_KID', $tag, '=');
    }
    
    $filterSparten = $sparten->condition($and)
     ->execute()
     ->fetchAll();
     
    foreach ($filterSparten as $sparte){
     $filteredEventIds[] = $sparte->hat_EID;
    }
    
   } // end Tag-Filter
   
   if (isset($filter['bezirke'])){
     
   foreach ($filter['bezirke'] as $bezirk) {
     
    $numFilters++;
    $bezirk_id = $this->clearContent($bezirk);
    $filteredBezirke[$bezirkId] = $bezirkId;
    
    $adressen = db_select($this->tbl_adresse, 'a')
     ->fields('a', array('ADID'))
     ->condition('bezirk', $bezirk_id, '=')
     ->execute()
     ->fetchAll();
     
    foreach ($adressen as $adresse) {
      
     $filterBezirke = db_select($this->tbl_event, 'e')
      ->fields('e', array('EID'))
      ->condition('ort', $adresse->ADID)
      ->execute()
      ->fetchAll();
      
     foreach ($filterBezirke as $bezirk) {
      $filteredEventIds[] = $bezirk->EID;
     }
     
    }
   }
  } // end Bezirke-Filter
  
  if (isset($filter['keyword'])) {
    
   $numFilters++;
   
   $or = db_or()
    ->condition('name', '%'.$filter['keyword'].'%', 'LIKE')
    ->condition('kurzbeschreibung', '%'.$filter['keyword'].'%', 'LIKE');
   
   $filterKeyword = db_select($this->tbl_event, 'e')
    ->fields('e', array('EID'))
    ->condition($or)
    ->execute()
    ->fetchAll();
    
   foreach ($filterKeyword as $keyword){
    $filteredEventIds[] = $keyword->EID;
   }
  } // end Keyword-Filter
  
  if (isset($filter['day'])) {
    
   $numFilters++;
   $resultDays = db_select($this->tbl_event, 'e')
    ->fields('e', array('EID'))
    ->condition('start_ts', $filter['day'].'%', 'LIKE')
    ->execute()
    ->fetchAll();
   foreach ($resultDays as $day){
    $filteredEventIds[] = $day->EID;
   }
  } // end Day-Filter
  
  if (!empty($filteredEventIds)) {
   $filteredEventChildrenIds = db_select($this->tbl_event, 'e')
    ->fields('e',array('EID'))
    ->condition('parent_EID', $filteredEventIds)
    ->execute();
    
   foreach ($filteredEventChildrenIds->fetchAll() as $child){
   # echo $filteredEventChildrenIds;
    $filteredEventIds[] = $child->EID;
   }
  }
  
  return $this->getDuplicates($filteredEventIds, $numFilters);
   
  }
  
  private function __beautifyPhoneNbr($nbr){
   $nbr = str_replace("---", "", trim($nbr));
   return str_replace("/", "-", $nbr);
  }

}
