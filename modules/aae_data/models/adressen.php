<?php

namespace Drupal\AaeData;

/**
*  Small helper-class that delivers methods
*  to GET, SET and REMOVE tags or clear tags-table
*
*/

Class adressen extends aae_data_helper {

 var $ADID;

 public function __construct() {
  
  parent::__construct();

 }

 public function getAllBezirke(){

   return db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->execute()
    ->fetchAll();

 }

 public function getAdresse($ADID){
  
  // Adresse + Bezirk - HATING DRUPAL JOINS IN PARTICULAR
  $resultAdresse = db_query('SELECT * FROM {aae_data_adresse} b INNER JOIN {aae_data_bezirke} bz ON bz.BID = b.bezirk WHERE b.ADID = :adresse', array(':adresse' => $ADID));
  return $resultAdresse->fetchObject();

 }

 public function setUpdateAdresse($adresse){
  
  $ADID = (isset($adresse->ADID) && !empty($adresse->ADID)) ? $adresse->ADID : NULL;

  // Adress already given?
  $this->resultAdresse = db_select($this->tbl_adresse, 'a')
   ->fields('a', array('ADID', 'gps_lat', 'gps_long'))
   ->condition('strasse', $adresse->strasse)
   ->condition('nr', $adresse->nr)
   ->condition('adresszusatz', $adresse->adresszusatz)
   ->condition('plz', $adresse->plz)
   ->condition('bezirk', $adresse->bezirk)
   ->execute();

  $rowCount = $this->resultAdresse->rowCount();
  $this->resultAdresse = $this->resultAdresse->fetchObject();
  $gps = explode(',', $adresse->gps, 2);

  // Wenn ja: Holen der ID der Adresse, wenn nein: einfügen
  if ($rowcount == 0 && !$ADID) {

   $this->ADID = db_insert($this->tbl_adresse)
    ->fields(array(
	 'strasse' => $adresse->strasse,
	 'nr' => $adresse->nr,
	 'adresszusatz' => $adresse->adresszusatz,
	 'plz' => $adresse->plz,
	 'bezirk' => $adresse->bezirk,
	 'gps_lat' => $gps[0],
     'gps_long' => $gps[1]
	 ))
	->execute();

    return $this->ADID;

  } else if ($rowcount == 1 && $this->resultAdresse->ADID == $ADID) {

   return $ADID;

  } else if ($rowcount == 0 && $ADID) {

   $this->ADID = db_update($this->tbl_adresse)
   ->fields(array(
    'strasse' => $adresse->strasse,
	'nr' => $adresse->nr,
	'adresszusatz' => $adresse->adresszusatz,
	'plz' => $adresse->plz,
	'bezirk' => $adresse->bezirk,
	'gps_lat' => $gps[0],
    'gps_long' => $gps[1]
	))
    ->condition('ADID', $ADID)
	->execute();

   return $ADID;

  }

 }

 
}