<?php
/**
 * eventprofil.php zeigt das Profil eines Events an.
 *
 * Ruth, 2015-07-10
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";
$tbl_adresse = "aae_data_adresse";
$tbl_event = "aae_data_event";
$tbl_hat_user = "aae_data_hat_user";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();
global $user;
//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$event_id = $explodedpath[1];

$resultakteurid = db_select($tbl_event, 'e')
  ->fields('e', array(
    'veranstalter',
  ))
  ->condition('EID', $event_id, '=')
  ->execute(); 
$akteur_id = "";
foreach ($resultakteurid as $row) {
  $akteur_id = $row->veranstalter;
}
//Prüfen ob Schreibrecht vorliegt
$resultUser = db_select($tbl_hat_user, 'u')
  ->fields('u', array(
    'hat_UID',
    'hat_AID',
  ))
  ->condition('hat_AID', $akteur_id, '=')
  ->condition('hat_UID', $user->uid, '=')
  ->execute();
$hat_recht = $resultUser->rowCount();

if(array_intersect(array('redakteur','administrator'), $user->roles)){
  $hat_recht = 1;
}

//Selektion der Eventinformationen
$resultevent = db_select($tbl_event, 'a')
  ->fields('a', array(
    'name',
    'veranstalter',
    'start',
    'ende',
    'kurzbeschreibung',
    'ort',
    'bild',
    'url',
  ))
  ->condition('EID', $event_id, '=')
  ->execute();

//-----------------------------------

//Ausgabe
$profileHTML = <<<EOF
EOF;



foreach($resultevent as $row){
	$profileHTML .= '<h1>'.$row->name.'</h1>';
	//Veranstalter
	$profileHTML .= '<h4>Veranstalter:</h4>';
	$resultakteur = db_select($tbl_akteur, 'c')
	  ->fields('c', array(
	    'name',
	  ))
	  ->condition('AID', $row->veranstalter, '=')
	  ->execute();
	foreach ($resultakteur as $row2) {
		$profileHTML .= '<a href="?q=Akteurprofil/'.$row->veranstalter.'">'.$row2->name.'</a><br>';
	}
	
	$profileHTML .= '<h4>Adresse:</h4>';
	//Adresse des Akteurs
	$resultadresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'ort',
	    'gps',
	  ))
	  ->condition('ADID', $row->ort, '=')
	  ->execute();
	foreach ($resultadresse as $row1) {
		$profileHTML .= $row1->strasse.' '.$row1->nr.'<br>';
		$profileHTML .= $row1->plz.' '.$row1->ort.'<br>';
		$profileHTML .= 'GPS: '.$row1->gps.'<br>';
	}
	//Datum
	$profileHTML .= '<h4>Zeit:</h4>';
	if($row->start != "") { 
	  $explodedstart = explode(' ', $row->start);
	  $profileHTML .= $explodedstart[0].'<br>';
	  $profileHTML .= $explodedstart[1].'-'.$explodedstart[2].'<br>';
	  if($row->ende != $explodedstart[0]){
		$profileHTML .= '- '.$row->ende.'<br>';
	  }
	}
	if($row->url != "") { $profileHTML .= '<br><a href="'.$row->url.'">'.$row->url.'</a><br>'; }
	if($row->kurzbeschreibung != "") { 
      $profileHTML .= '<h4>Beschreibung:</h4>';
	  $profileHTML .= $row->kurzbeschreibung.'<br>';
	}
	//Bild
	if($row->bild != "") { 
	  $profileHTML .= '<img src="sites/all/modules/aae_data/'.$row->bild.'" >'; }
	if($hat_recht == 1){
      $profileHTML .= '<a href="?q=Eventloeschen/'.$event_id.'" >'.Löschen.'</a>'.'   '.'<a href="?q=Eventedit/'.$event_id.'" >'.Bearbeiten.'</a>';
    }
}
