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
 public function isAuthorized($aId, $uId = NULL){

  global $user;

  $uId = (empty($uId) ? $this->user_id : $uId);
  
  $resultUser = db_select($this->tbl_hat_user, 'u')
   ->fields('u')
   ->condition('hat_AID', $aId)
   ->condition('hat_UID', $uId)
   ->execute();

  if ($resultUser->rowCount() || in_array('administrator', $user->roles)) {
   return true;
  } else {
   return false;
  }
  
 }

 public function akteurExists($aId){

  $resultAkteur = db_select($this->tbl_akteur, 'a')
   ->fields('a', array('AID'))
   ->condition('AID', $aId)
   ->execute();

  return $resultAkteur->rowCount();

 }
 
 /*
  * @return Akteure-object, keyed by AID
  * @param $condition : array : see akteurepage.php for examples
  * @param $fields : integer : MINIMAL output (=preview-mode)
  *                            NORMAL output
  *
  */
 public function getAkteure($conditions = NULL, $fields = 'normal', $orderBy = 'name') {
  
  $akteure = db_select($this->tbl_akteur, 'a');

  if ($fields == 'minimal'){
   $akteure->fields('a', array('AID','name','beschreibung','bild','adresse'));
  } else {
   $akteure->fields('a');
  }

  foreach ($conditions as $key => $condition){
   
   switch ($key) {
    
    case ('range') :
     $akteure->range($condition['start'],$condition['end']);
    break;

    case('filter') :
     if (!empty($conditions['filter']))
       $akteure->condition('AID', $this->__filterAkteure($conditions['filter']));
    break;

    default :
     $akteure->condition($key, $condition);
    break;

   }

  }

  $akteure->orderBy('created', DESC)
          ->orderBy('name', ASC); #TODO: make dynamic

  $resultAkteure = $akteure->execute()->fetchAllAssoc('AID');

  foreach ($resultAkteure as $counter => $akteur){

   $numwords = 30;
   preg_match("/(\S+\s*){0,$numwords}/", $akteur->beschreibung, $regs);

   $adresse = db_select($this->tbl_adresse, 'ad');
   if ($fields == 'complete'){
     $adresse->fields('ad');
   } else {
     $adresse->fields('ad', array('bezirk','gps_lat','gps_long'));
   }

   $adresse = $adresse->condition('ADID', $akteur->adresse)->execute()->fetchObject();

   $bezirk = db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->condition('BID', $adresse->bezirk)
    ->execute()
    ->fetchObject();

   // Hack: add variable to $resultAkteure-object
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['adresse'] = $adresse;
   $resultAkteure[$counter]['bezirk'] = $bezirk;
   $resultAkteure[$counter]['gps'] = ($adresse->gps_lat != 'Ermittle Geo-Koordinaten...' && !empty($adresse->gps_lat) ? $adresse->gps_lat.','.$adresse->gps_long : '');
   $resultAkteure[$counter]['kurzbeschreibung'] = trim($regs[0]);

   if ($fields == 'complete'){

    // get Tags
    $resultTags = db_select($this->tbl_hat_sparte, 'ht')
     ->fields('ht')
     ->condition('hat_AID', $akteur->AID)
     ->execute()
     ->fetchAll();

    $tags = array();

    foreach($resultTags as $tag) {

     $tags[] = db_select($this->tbl_sparte, 's')
     ->fields('s')
     ->condition('KID', $tag->hat_KID)
     ->execute()
     ->fetchAll();

    }

    $resultAkteure[$counter]['tags'] = $tags;
    
   }

   $resultAkteure[$counter] = (object)$resultAkteure[$counter];

  }

  return $resultAkteure;

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
   
  } // end Tags-Filter

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
  
  return $this->getDuplicates($filteredAkteurIds, $numFilters);
   
 }
 
}