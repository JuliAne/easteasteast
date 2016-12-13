<?php

namespace Drupal\AaeData;

/**
*  Small helper-class that delivers methods
*  to GET, SET and REMOVE tags or clear tags-table
*
*  TODO: Bis Version 1 den unsäglichen Begriff der Sparten ersetzen
*  TODO: Automatisierte actions auf xy_hat_sparte-tables
*/

Class tags extends aae_data_helper {

 public function __construct() {
  
  parent::__construct();

 }

 /**
  * function getTags()
  *
  * @param $type : string [akteure/events]
  * @param $condition : array
  * @return all given tags (/for $type)
  *
  * TODO (if required): Make multiple $conditions possible
  */
 public function getTags($type = NULL, $condition = NULL) {

   if ($type == 'events'){

     $tags = db_query('SELECT * FROM {aae_data_sparte} s LEFT JOIN {aae_data_event_hat_sparte} ehs ON s.KID = ehs.hat_KID
                      '. (!empty($condition) ? ' WHERE ehs.'. $condition[0] .' = '. $condition[1] .'. ' : '').'
                       GROUP BY s.kategorie DESC');

   } else if ($type == 'akteure') {

     $tags = db_query('SELECT * FROM {aae_data_sparte} s LEFT JOIN {aae_data_akteur_hat_sparte} ahs ON s.KID = ahs.hat_KID
                      '. (!empty($condition) ? ' WHERE ahs.'. $condition[0] .' = '. $condition[1] .'. ' : '').'
                       GROUP BY s.kategorie DESC');
     
   } else {

     $tags = db_select($this->tbl_sparte, 't')
     ->fields('t')
     ->execute();

   }

   return $tags->fetchAll();
   # ggf. return $tags->fetchAllAssoc('KID');

 }
 
 /**
  * Method that unifies SET, UPDATE and REMOVE actions for tags
  * and interacts with hat-xy-table's
  *
  * @param $tags : array
  * @param $target : array (set's join-tables & will be automated in future times)
  * @param $removedTags : array [opt]
  * @returnTODO $collectedTags (array)
  */
 public function setRemoveTags($tags, $target, $removedTags = null){

  if ($target[0] == 'akteur'){
    
    $targetHatTbl = $this->tbl_hat_sparte;
    $targetColumn = 'hat_AID';
    $targetId = $target[1];  

  } else if ($target[0] == 'event'){

    $targetHatTbl = $this->tbl_event_sparte;
    $targetColumn = 'hat_EID';
    $targetId = $target[1]; 

  }

  if (!empty($removedTags) && is_array($removedTags)) {

    foreach ($removedTags as $tag) {

     $tag = $this->clearContent($tag);

     db_delete($targetHatTbl)
      ->condition('hat_KID', $tag)
      ->condition($targetColumn, $targetId)
      ->execute();

     if (!$this->isTagUsed($tag)){

      db_delete($this->tbl_sparte)
       ->condition('KID', $tag)
       ->execute();

     }

   }
  }

  // UPDATE or INSERT tags (if required)
  if (!empty($tags) && is_array($tags)) {

   $collectedTags = array(); # Yet unused, should be 'kategoriename' => 'KID'

   $tags = array_unique($tags);

   foreach ($tags as $tag) {

    $tagId = '';
    $tag = str_replace('#','',strtolower($this->clearContent($tag)));

    if (empty($tag) /*|| !empty($collectedTags[$tag])*/) {
     break;
    }
      
  	$tagQuery = db_select($this->tbl_sparte, 's')
  	 ->fields('s')
  	 ->condition('KID', $tag)
  	 ->execute();

    // Tag already existing?...
    if ($tagQuery->rowCount() == 0) { // HIER WAS LOS?!

     $kategorieQuery = db_select($this->tbl_sparte, 's')
      ->fields('s')
      ->condition('kategorie', $tag)
      ->execute();

     if ($kategorieQuery->rowCount() == 0){

      // ...Nope!
      $tagId = db_insert($this->tbl_sparte)
  	   ->fields(array('kategorie' => $tag))
  	 	 ->execute();

      $collectedTags[$tag] = $tag;

     } else {

      // ...YIP, as kategorie (=string)
      $kategorieQuery = $kategorieQuery->fetchObject();
      $tagId = $kategorieQuery->KID;
      $collectedTags[$kategorieQuery->kategorie] = $kategorieQuery->kategorie;

     }

  	} else {
     
     // ...YIP!
     $tagQuery = $tagQuery->fetchObject();
  	 $tagId = $tagQuery->KID;
     $collectedTags[$tagQuery->kategorie] = $tagQuery->kategorie;

  	}

    // Tag already adressed to target?
    $hasTargetTag = db_select($targetHatTbl, 'ht')
     ->fields('ht')
     ->condition('hat_KID', $tagId)
     ->condition($targetColumn, $targetId)
     ->execute();

    if ($hasTargetTag->rowCount() == 0) {

     db_insert($targetHatTbl)
     ->fields(array(
      $targetColumn => $targetId,
      'hat_KID' => $tagId
      ))
     ->execute();

    }

    #module_invoke_all('hook_tag_added', $tagId);

   }
  }

 }

 /* 
    @function removeDoubleTags()
    Removes multiple-assessed tags or empty tags 
    @return $removedTagIds : integer
    TODO: Ggf. weitere xy_hat_sparte-tables berücksichtigen
    TODO: Avoid PDOException / refactor
 */
 public function removeDoubleTags(){

  $collectedTags = array();  
  $removedTagIds = array();

  foreach ($this->getTags() as $tag){

   $tag->kategorie = trim(strtolower($tag->kategorie));
   
   if (isset($collectedTags[$tag->kategorie])) {
    
    // akteur_hat-tbl
    $availAkteurTags = db_select($this->tbl_hat_sparte, 'hs')
     ->fields('hs')
     ->condition('hat_KID', $tag->KID)
     ->execute();
     
    foreach ($availAkteurTags->fetchAll() as $aTag) {
     if (!db_select($this->tbl_hat_sparte,'hs')->fields('hs')->condition('hat_AID',$aTag->hat_AID)->condition('hat_KID',$collectedTags[$tag->kategorie])->execute()->rowCount()){
      db_update($this->tbl_hat_sparte)
       ->fields(array(
       'hat_KID' => $collectedTags[$tag->kategorie]
       ))
       ->condition('hat_KID', $tag->KID)
       ->execute();
     }
    }
     
    // event_hat-tbl
    $availEventTags = db_select($this->tbl_event_sparte, 'es')
     ->fields('es')
     ->condition('hat_KID', $tag->KID)
     ->execute();
    
    foreach ($availEventTags->fetchAll() as $eTag) {
     if (!db_select($this->tbl_event_sparte,'es')->fields('es')->condition('hat_EID',$eTag->hat_EID)->condition('hat_KID',$collectedTags[$tag->kategorie])->execute()->rowCount()){
      db_update($this->tbl_event_sparte)
       ->fields(array(
       'hat_KID' => $collectedTags[$tag->kategorie]
       ))
       ->condition('hat_KID', $tag->KID)
       ->execute();
     }
    }
     
    $removedTagIds[] = db_delete($this->tbl_sparte)
     ->condition('KID', $tag->KID)
     ->execute();
    
   } else {
 
    $collectedTags[$tag->kategorie] = $tag->KID;
   
   } 
  }

  // Check for empty-tags (still an issue, check bugtracker...)
 # $emptyTags = db_query('SELECT * FROM {aae_data_sparte} s WHERE s.kategorie IS NULL');
  
  $emptyTags = db_query('SELECT * FROM {aae_data_akteur_hat_sparte} hs
                         LEFT OUTER JOIN {aae_data_sparte} s
                         ON (s.KID = hs.hat_KID)
                         WHERE s.KID IS NULL');

  foreach ($emptyTags->fetchAll() as $tag){
   
    db_delete($this->tbl_hat_sparte)
     ->condition('hat_KID', $tag->hat_KID)
     ->execute();

    $removedTagIds[] = $tag->hat_KID;

  }

  $emptyTags = db_query('SELECT * FROM {aae_data_event_hat_sparte} hs
                         LEFT OUTER JOIN {aae_data_sparte} s
                         ON (s.KID = hs.hat_KID)
                         WHERE s.KID IS NULL');

  foreach ($emptyTags->fetchAll() as $tag){
   
   db_delete($this->tbl_event_sparte)
    ->condition('hat_KID', $tag->hat_KID)
    ->execute();

   $removedTagIds[] = $tag->hat_KID;

  }

   /* db_delete($this->tbl_hat_sparte)
     ->condition('hat_KID', $tag->KID)
     ->execute();

    db_delete($this->tbl_event_sparte)
     ->condition('hat_KID', $tag->KID)
     ->execute(); */

  return $removedTagIds;

 }

 /* Checks if there is any use of that tag in various has-tables
    @param $tagId : integer
    @return boolean;
    TODO: Ggf. weitere xy_hat_sparte-tables berücksichtigen
  */

 private function isTagUsed($tagId){
  
  $resultForAkteure = db_select($this->tbl_hat_sparte, 'as')
   ->fields('as')
   ->condition('hat_KID', $tagId)
   ->execute();

  $resultForEvents = db_select($this->tbl_event_sparte, 'es')
   ->fields('es')
   ->condition('hat_KID', $tagId)
   ->execute();

  if (!$resultForAkteure->rowcount() && !$resultForEvents->rowCount()){
   return false;
  }

  return true;

 }

 /**
  * Method that assigns $tag-ids the value of their names ("kategorie").
  * If no cat was found, the key will be a string
  * @param $tags : array
  * @return $newTags : object
  */
 public function __getKategorieForTags($tags) {

  $newTags = array();
  $tags = array_unique($tags);

  foreach ($tags as $tag) {

   $tag = strtolower($this->clearContent($tag));

   if (is_numeric($tag)) {

     $tagName = db_select($this->tbl_sparte, 's')
      ->fields('s', array('kategorie'))
      ->condition('KID', $tag)
      ->execute()
      ->fetchObject();

     $newTags[] = (object)array('KID' => $tag, 'kategorie' => $tagName->kategorie);

   } else {

    $newTags[] = (object)array('KID' => $tag, 'kategorie' => $tag);

   }
  }

  return $newTags;

 }
 
}