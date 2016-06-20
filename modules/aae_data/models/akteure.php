<?php

namespace Drupal\AaeData;

/*
*  Small wannabe-model class that delivers methods
*  for getting and manipulating akteure-data.
*   
*  @use use \Drupal\AaeData\akteure()
*       $this->akteure = new akteure();
*/

Class akteure extends aae_data_helper {

 public function __construct() {
  parent::__construct();
 }
 
 /*
  * Checks whether user owns akteur
  * @return boolean
 */
 public function isAuthorized($aId, $uId){
     
  global $user;
  
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $aId)
   ->condition('hat_UID', $this->user_id)
   ->execute();

  if ($resultUser->rowCount() || !array_intersect(array('redakteur','administrator'), $user->roles)) {
   return true;
  } else {
   return false;
  }
  
 }
 
 /*
  * @return Akteure-object, keyed by AID
  * @param $condition : array
  *
  */

 public function getAkteure($conditions = NULL, $fields = 'normal', $orderBy = 'name') {
  # TODO
 }
 
 public function setAkteur($data){
  # TODO
 }
 
 public function removeAkteur($aId){
     
  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('name','bild'))
   ->condition('AID', $aId)
   ->execute()
   ->fetchObject();
  
  $resultEvents = db_select($this->tbl_akteur_events, 'ae')
  ->fields('ae')
  ->condition('AID', $aId)
  ->execute()
  ->fetchAll();

 foreach($resultEvents as $event){
  db_delete($this->tbl_event)
   ->condition('EID', $event->EID)
   ->execute();
 }

 db_delete($this->tbl_akteur_events)
  ->condition('AID', $aId)
  ->execute();

 db_delete($this->tbl_hat_user)
  ->condition('hat_AID', $aId)
  ->execute();

 db_delete($this->tbl_akteur)
  ->condition('AID', $aId)
  ->execute();

 db_delete($this->tbl_hat_sparte)
  ->condition('hat_AID', $aId)
  ->execute();

 // remove profile-image (if possible)
 $bild = end(explode('/', $resultAkteur->bild));

 if (file_exists($this->short_bildpfad.$bild)) {
  unlink($this->short_bildpfad.$bild);
 }

 menu_link_delete(NULL, 'akteurprofil/'.$aId);
  
 }
 
 private function __filterAkteure($filter){
  
  $filteredAkteurIds = array();
  $numFilter = 0;
  $filteredTags = array();
  $filteredBezirke = array();
  
  if (isset($filter['tags'])){

   $sparten = db_select($this->tbl_hat_sparte, 'hs')
    ->fields('hs', array('hat_AID'));

   $and = db_and();

   foreach ($filter['tags'] as $tag) {

    $numFilters++;
    $tag = $this->clearContent($tag);
    $filteredTags[$tag] = $tag;
    $and->condition('hat_KID', $tag);

   }

   $filterSparten = $sparten->condition($and)
    ->execute()
    ->fetchAll();

   foreach ($filterSparten as $sparte){
    $filteredAkteurIds[] = $sparte->hat_AID;
   }
   
  } // end Tag-Filter

  if (isset($filter['bezirke'])){

   foreach ($filter['bezirke'] as $bezirk) {

    $numFilters++;
    $bezirkId = $this->clearContent($bezirk);
    $filteredBezirke[$bezirkId] = $bezirkId;

    $adressen = db_select($this->tbl_adresse, 'a')
     ->fields('a', array('ADID'))
     ->condition('bezirk', $bezirkId)
     ->execute()
     ->fetchAll();

    foreach ($adressen as $adresse) {

     $filterBezirke = db_select($this->tbl_akteur, 'a')
      ->fields('a', array('AID'))
      ->condition('adresse', $adresse->ADID)
      ->execute()
      ->fetchAll();

     foreach ($filterBezirke as $bezirk) {
      $filteredAkteurIds[] = $bezirk->AID;
     }
    }
   }
  } // end Bezirke-Filter

  if (isset($filter['keyword'])) {

   $numFilters++;

   $or = db_or()
    ->condition('name', '%'.$filter['keyword'].'%', 'LIKE')
    ->condition('beschreibung', '%'.$filter['keyword'].'%', 'LIKE');

   $filterKeyword = db_select($this->tbl_akteur, 'e')
    ->fields('e', array('AID'))
    ->condition($or)
    ->execute()
    ->fetchAll();

   foreach ($filterKeyword as $keyword){
    $filteredAkteurIds[] = $keyword->AID;
   }
  } // end Keyword-Filter
  
 
  if (!empty($filteredEventIds)) {
   $filteredEventChildrenIds = db_select($this->tbl_event, 'e')
    ->fields('e',array('EID'))
    ->condition('parent_EID', $filteredEventIds)
    ->execute();
    
   foreach ($filteredEventChildrenIds->fetchAll() as $child){
    $filteredEventIds[] = $child->EID;
   }
  }
  
  return $this->getDuplicates($filteredEventIds, $numFilters);
   
  
 }
 
}