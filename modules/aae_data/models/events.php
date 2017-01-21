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

 var $allowDuplicates = false; // Will we filter (=reduce) the results [default] or count together (maximize)

 public function __construct() {

   parent::__construct();
   $this->tagsHelper   = new tags();
   $this->adressHelper = new adressen();

 }

 public function setEvent($data) {
  # TODO, s. akteurformular
 }

 /**
  * @return Event-object, keyed by EID (for performance purposes when using children)
  *         start, ende, created, modified & eventRecurresTill: DateTime-objects
  * @param $conditions : array : Custom operators supported
  * @param $fields : integer: EID (= EID only)
  *                           MINIMAL output (= puuurfect for date-calculation),
  *                           NORMAL output (= full table-row),
  *                           COMPLETE (= joins all other tables)
  */
 public function getEvents($conditions = NULL, $fields = 'normal', $calledRecursively = false, $orderBy = 'ASC') {
   
   $events = db_select($this->tbl_event, 'e');
   
   if ($fields == 'EID'){
    $events->fields('e', array('EID'));
   } else if ($fields == 'minimal') {
    $events->fields('e', array('EID','name','ersteller','start_ts','ende_ts','parent_EID','created','modified'));
   } else {
    $events->fields('e');
   }

   foreach ($conditions as $key => $condition) {

    switch ($key) {
      
     case ('limit') :
     
     $events->range(0, $condition);
     
     break;
      
     case ('start_ts') :
     case ('start') :
     
     // Make multiple start-times possible
     if (is_array($condition)) {
       
      foreach ($condition as $s) {
       $events->condition('start_ts', $s['date'], $s['operator']);
      }
      
     } else if (!empty($condition)) {
      $events->condition('start_ts', $condition, '>=');
     }
     break;
      
     case ('ende_ts') :
     case ('end') :
      $events->condition('ende_ts', $condition, '<=');
      $events->condition('ende_ts', '1000-01-01 00:00:00', '!=');
      // TODO for API: Make multiple end-values possible, as above
      break;
      
     case ('filter') :
      $events->condition('EID', $this->__filterEvents($conditions['filter'], $fields));
     break;
     
     default :

      if (is_array($condition) && $key != 'EID' && $key != 'ort') {
      // The above's not very beautiful, 2b changed pls
       foreach ($condition as $s){
        $events->condition($s['key'], $s['condition'], $s['operator']);
       }
      } else {
       $events->condition($key, $condition);
      }
      break;

    }

   }

   $events->orderBy('start_ts', $orderBy);
   $datas = $events->execute()->fetchAllAssoc('EID');

   // Format & add specific data from other tables...

   foreach ($datas as $event) {
     
    $realEID = $event->EID;

    if (!empty($event->parent_EID) && !$calledRecursively){
 
     // Inherit from parent
     $parentData = db_select($this->tbl_event,'e')->fields('e')->condition('EID', $event->parent_EID);
     
     $datas[$realEID] = (isset($datas[$event->parent_EID]) && !empty($datas[$event->parent_EID]))
     ? $datas[$event->parent_EID]
     : $parentData->execute()->fetchAssoc(); # trigger DB-action $parentData

     $event->EID = $event->parent_EID;

    }

    // Hack: add variables to $datas-object
    $datas[$realEID] = (array)$datas[$realEID];

    if ($fields == 'complete') {
      
     if ($event->recurring_event_type >= 1) {

      $childrenEvents = $this->getEvents(array('parent_EID' => $conditions['EID']), 'minimal', true);
      if (!empty($childrenEvents))
        $datas[$realEID]['childrenEvents'] = $childrenEvents;
     
     }

     $ersteller = db_select('users', 'u')
      ->fields('u', array('name'))
      ->condition('uid', $event->ersteller)
      ->execute()
      ->fetchObject();

     $datas[$realEID]['ersteller'] = $ersteller->name;
     $datas[$realEID]['adresse'] = $this->adressHelper->getAdresse($event->ort);

     $datas[$realEID]['tags'] = $this->tagsHelper->getTags('events', array('hat_EID', $event->EID));

   }
   
   if ($fields == 'complete' || $fields == 'normal') {

     $akteurId = db_select($this->tbl_akteur_events, 'ae')
      ->fields('ae', array('AID'))
      ->condition('EID', $event->EID)
      ->execute()
      ->fetchObject();

     $this->akteur = new akteure();
    
     $datas[$realEID]['akteur'] = $this->akteur->getAkteure(array('AID' => $akteurId->AID),'minimal')[$akteurId->AID];
    
     if (!empty($event->FID)) {
      // TODO: Outsource into festivals-model, ->get('normal'), check akteure-models first
      $resultFestival = db_select($this->tbl_festival, 'f')
       ->fields('f')
       ->condition('FID', $event->FID)
       ->execute()
       ->fetchObject();

      $datas[$realEID]['festival'] = $resultFestival;
     }
     
    }

    $datas[$realEID]['start'] = new \DateTime($event->start_ts);
    $datas[$realEID]['ende'] = new \DateTime($event->ende_ts);
    $datas[$realEID]['created'] = new \DateTime($event->created);
    $datas[$realEID]['modified'] = new \DateTime($event->modified);
    $datas[$realEID]['eventRecurresTill'] = new \DateTime($event->event_recurres_till);
    $datas[$realEID]['eventRecurringType'] = $event->recurring_event_type;
    $datas[$realEID] = (object)$datas[$realEID];

  }

  return $datas;

 }
 
 /* 
 *  Checks whether user has permission to edit event
 *
 *  @return boolean
 *  @param event_id
 *  @param user_id (optional)
 *
 */
 public function isAuthorized($eId, $uId = NULL){

  global $user;

  $uId = (empty($uId) ? $this->user_id : $uId);
  
  $erstellerId = db_select($this->tbl_event,'e')
   ->fields('e', array('ersteller'))
   ->condition('EID', $eId)
   ->execute();
   
  $erstellerId = $erstellerId->fetchObject();
  
  if ($erstellerId->ersteller == $uId || in_array('administrator', $user->roles)){
   return true;
  }
 
  $resultAkteurId = db_select($this->tbl_akteur_events, 'e')
   ->fields('e', array('AID'))
   ->condition('EID', $eId)
   ->execute()
   ->fetchObject();
  
  $resultAkteurHasUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $resultAkteurId->AID)
   ->condition('hat_UID', $uId)
   ->execute();
   
  if ($resultAkteurHasUser->rowCount() == 1) {
   return true;
  } else {
   return false;
  }
  
 }
 
 /**
  * returns festival-ids and aliase that user has access to
  * TODO: Put into festivals-model
  */
 public function userHasFestivals($uid){

  global $user;
  
  $resultFestivals = array();
  $fIds = array(); // Helper to avoid doubled festival-IDs (e.g. if user owns multiple akteure who manage a festival)

  $resultUserAkteure = db_select($this->tbl_hat_user, 'hu')
   ->fields('hu', array('hat_AID'));

  if (!in_array('administrator', $user->roles)) {
   $resultUserAkteure->condition('hat_UID', $uid);
  }

  $resultUserAkteure = $resultUserAkteure->execute()->fetchAll();

  if (!empty($resultUserAkteure)){

  foreach ($resultUserAkteure as $akteur){

   $akteurHasFestivals = db_select($this->tbl_hat_festivals, 'hf')
   ->fields('hf', array('hat_FID'))
   ->condition('hat_AID', $akteur->hat_AID)
   ->execute()
   ->fetchAll();
   
   foreach ($akteurHasFestivals as $festival){
    if (!isset($fIds[$festival->hat_FID])){
      
     $fIds[$festival->hat_FID] = 1;

     $fname = db_select($this->tbl_festival, 'f')
      ->fields('f', array('name'))
      ->condition('FID', $festival->hat_FID)
      ->execute()
      ->fetchObject();

     $festivalIds[] = array(
      'FID' => $festival->hat_FID,
      'name' => $fname->name,
     );

    }
   }

  }
  }
  
  return $festivalIds;

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

  public function __removeEvent($event_id){

   $data = db_select($this->tbl_event, 'e')
    ->fields('e', array('bild','recurring_event_type'))
    ->condition('EID', $event_id)
    ->execute()
    ->fetchObject();

   db_delete($this->tbl_akteur_events)
    ->condition('EID', $event_id)
    ->execute();

   db_delete($this->tbl_event)
    ->condition('EID', $event_id)
    ->execute();

   db_delete($this->tbl_event_sparte)
   ->condition('hat_EID', $event_id)
   ->execute();

   // remove children-items, if given
   if (!empty($data->recurring_event_type)) {
    $this->removeEventChildren($event_id);
   }

   // remove profile-image
   // To be improved soon
   $bild = end(explode('/', $data->bild));

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
  
  public function accessibleAkteure($uId){
    
  }
  
  private function __filterEvents($filter, $fields = NULL){
    
   $filteredEventIds = array();
   $numFilter = 0;
   $filteredTags = array();
   $filteredBezirke = array();
   $filteredTags = array();

   if (isset($filter['mustHaveGps'])){

    $numFilters++;

    $datas = db_query(
    "SELECT EID, ADID
     FROM {aae_data_adresse} ad
     JOIN {aae_data_event} e
     WHERE ad.gps_long != '' AND ad.gps_lat != '' AND ad.ADID = e.ort");

    foreach ($datas->fetchAll() as $event){
     $filteredEventIds[] = $event->EID;
    }

   } // end empty-GPS-jumper
   
   if (isset($filter['tags'])){
   
    $tags = db_select($this->tbl_event_sparte, 'hs')
     ->fields('hs', array('hat_EID'));

    $and = db_and();
   
    foreach ($filter['tags'] as $tag) {
     $tag = $this->clearContent($tag);
     $filteredTags[$tag] = $tag;
     $and->condition('hat_KID', $tag);
     $numFilters++;
    }
    
    $filterTags = $tags->condition($and)
     ->execute()
     ->fetchAll();
     
    foreach ($filterTags as $tag){
     $filteredEventIds[] = $tag->hat_EID;
    }
    
   } // end Tag-Filter
   
   if (isset($filter['bezirke'])){
     
   foreach ($filter['bezirke'] as $bezirk) {
     
    $numFilters++;
    $bezirk_id = $this->clearContent($bezirk);
    $filteredBezirke[$bezirkId] = $bezirkId;
    
    $adressen = db_select($this->tbl_adresse, 'a')
     ->fields('a', array('ADID'))
     ->condition('bezirk', $bezirk_id)
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

   // Search for the exact date OR multiple-days-events in between
    
   $numFilters++;

   $resultDays  = db_query('SELECT EID FROM {aae_data_event} WHERE (start_ts <= :start AND ende_ts >= :start AND ende_ts NOT LIKE :zeroLike) OR start_ts LIKE :startLike', array(':start' => $filter['day'], ':startLike' => $filter['day'].'%', ':zeroLike' => '1000-01-01 00:00:0%'));

   foreach ($resultDays as $day){
    $filteredEventIds[] = $day->EID;
   }

  } // end Day-Filter
  
  if (isset($filter['AID'])) {

   if (!is_array($filter['AID']))
    $filter['AID'] = array($filter['AID']);

   $numFilters++;

   foreach ($filter['AID'] as $aid){

    $resultAkteur = db_select($this->tbl_akteur_events, 'ae')
     ->fields('ae')
     ->condition('AID', $this->clearContent($aid))
     ->execute()
     ->fetchAll();
    
    foreach ($resultAkteur as $akteur){
     $filteredEventIds[] = $akteur->EID;
    }

   } 
   
  } // end AkteurID-Filter

  if (isset($filter['UID'])) {
   // Filter for events marked as "private" OR created by user

   if (!is_array($filter['UID']))
    $filter['UID'] = array($filter['UID']);

   $numFilters++;

   foreach ($filter['UID'] as $uid){

    $resultUser = db_select($this->tbl_event, 'e')
     ->fields('e')
     ->condition('ersteller', $this->clearContent($uid))
     ->execute()
     ->fetchAll();
    
    foreach ($resultUser as $user){
     $filteredEventIds[] = $user->EID;
    }

   } 
   
  } // end UserID-Filter
  
  if (!empty($filteredEventIds) && $fields == 'complete') {
   $filteredEventChildrenIds = db_select($this->tbl_event, 'e')
    ->fields('e',array('EID'))
    ->condition('parent_EID', $filteredEventIds)
    ->execute();
    
   foreach ($filteredEventChildrenIds->fetchAll() as $child){
    $filteredEventIds[] = $child->EID;
   }
  }

  if ($this->allowDuplicates){
   return $filteredEventIds;
  } else {
  return $this->getDuplicates($filteredEventIds, $numFilters);
  }

 }
 
 // TODO: Unnötiges Zeugs rauswerfen, am besten nach output von $data orientieren!
 protected function __setSingleEventVars($data){

   $this->akteur = $data->akteur;
   $this->festival = $data->festival;
   $this->akteur_id = $data->akteur->AID;
   $this->has_starting_time = ($data->start->format('s') == '01') ? true : false;
   $this->has_ending_time = ($data->ende->format('s') == '01') ? true : false;
   $this->name = $data->name;
   $this->ort = $data->ort;
   $this->start = $data->start; #->format('Y-m-d');
   $this->ende = $data->ende; #->format('Y-m-d');
   $this->starting_time = $data->start->format('H:i');
   $this->ending_time = $data->ende->format('H:i');
   $this->url = $data->url;
   $this->bild = $data->bild;
   $this->kurzbeschreibung = $data->kurzbeschreibung;
   $this->created = $data->created;
   $this->modified = $data->modified;
   $this->eventRecurres = ($data->recurring_event_type >= 1);
   $this->recurringEventType = $data->recurring_event_type;
   $this->eventRecurresTill = $data->eventRecurresTill->format('Y-m-d');
	 $this->adresse = $data->adresse;
   $this->adresse->gps = (!empty($data->adresse->gps_lat)) ? $data->adresse->gps_lat.','.$data->adresse->gps_long : '';
   $this->FID = $data->FID;
   $this->ersteller = $data->ersteller;
  // $this->ersteller = user_load($data->ersteller);
   $this->tags = $data->tags;

 }

}
