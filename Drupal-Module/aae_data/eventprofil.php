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
$tbl_akteur_events = "aae_data_akteur_hat_events";
$tbl_bezirke = "aae_data_bezirke";
$tbl_sparte = "aae_data_kategorie";
$tbl_event_sparte = "aae_data_event_hat_sparte";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();
global $user;
//EID holen:
$path = current_path();
$explodedpath = explode("/", $path);
$event_id = $explodedpath[1];

//Prüfen, wer Schreibrechte hat
//Sicherheitsschutz, ob User entsprechende Rechte hat
$resultakteurid = db_select($tbl_akteur_events, 'e')//Den Akteur zum Event aus DB holen
  ->fields('e', array(
    'AID',
  ))
  ->condition('EID', $event_id, '=')
  ->execute(); 
$akteur_id = "";
$okay="";//gibt an, ob Zugang erlaubt wird oder nicht
foreach ($resultakteurid as $row) {
  $akteur_id = $row->AID;//Akteur speichern
  //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
  $resultUser = db_select($tbl_hat_user, 'u')
    ->fields('u', array(
      'hat_UID',
      'hat_AID',
    ))
    ->condition('hat_AID', $akteur_id, '=')
    ->condition('hat_UID', $user_id, '=')
    ->execute();
  $hat_recht = $resultUser->rowCount();
  if($hat_recht == 1){//User gehört zu Akteur
	$okay = 1;//Zugang erlaubt
  }
}
//Abfrage, ob User Ersteller des Events ist:
$ersteller = db_select($tbl_event, 'e')
  ->fields('e', array(
    'ersteller',
  ))
  ->condition('ersteller', $user->uid, '=')
  ->execute();
$ist_ersteller = $ersteller->rowCount();
if($ist_ersteller == 1){
	$okay =1;
}
 
if(array_intersect(array('administrator'), $user->roles)){
  $okay = 1;
}


//Selektion der Eventinformationen
$resultevent = db_select($tbl_event, 'a')
  ->fields('a', array(
    'name',
    'start',
    'ende',
    'kurzbeschreibung',
    'ort',
    'bild',
    'url',
    'ersteller',
  ))
  ->condition('EID', $event_id, '=')
  ->execute();
$resultveranstalter = db_select($tbl_akteur_events, 'e')
  ->fields('e', array(
    'AID',
  ))
  ->condition('EID', $event_id, '=')
  ->execute();

//Selektion der Tags
$resultsparten = db_select($tbl_event_sparte, 's')
  ->fields('s', array(
    'hat_KID',
  ))
  ->condition('hat_EID', $event_id, '=')
  ->execute();
$countsparten = $resultsparten->rowCount();
$sparten = array();
$i = 0;
if($countsparten != 0){
	foreach ($resultsparten as $row) {
	  $resultspartenname = db_select($tbl_sparte, 'p')
	  ->fields('p', array(
	    'kategorie',
	  ))
	  ->condition('KID', $row->hat_KID, '=')
	  ->execute();
	  foreach ($resultspartenname as $row1) {
		$sparten[$i] = $row1->kategorie;
	  }
	  $i = $i+1;
	}
	
}

//-----------------------------------

//Ausgabe
$profileHTML = <<<EOF
EOF;

foreach($resultevent as $row){
	$profileHTML .= '<h1>'.$row->name.'</h1>';
	
	//Veranstalter
	if($resultveranstalter->rowCount() != 0){
	$profileHTML .= '<h4>Veranstalter:</h4>';
	foreach ($resultveranstalter as $row1) {
	  $resultakteur = db_select($tbl_akteur, 'b')
	  ->fields('b', array(
	    'name',
	  ))
	  ->condition('AID', $row1->AID, '=')
	  ->execute();
	  foreach ($resultakteur as $row2) {
		$profileHTML .= '<a href="?q=Akteurprofil/'.$row1->AID.'">'.$row2->name.'</a><br>';
	  }
	}
	}
	
	//Ersteller aus DB holen
	$ersteller = db_select("users", 'u')
	->fields('u', array(
	  'name',
	))
	->condition('uid', $row->ersteller, '=')
	->execute();
	foreach ($ersteller as $row2) {
		$profileHTML .= '<p>Erstellt von: '.$row2->name.'</p>';
	}
	
	//Adresse des Akteurs
	$resultadresse = db_select($tbl_adresse, 'b')
	  ->fields('b', array(
	    'strasse',
	    'nr',
	    'plz',
	    'bezirk',
	    'gps',
	  ))
	  ->condition('ADID', $row->ort, '=')
	  ->execute();
	if($resultadresse->rowCount() != 0){
	$profileHTML .= '<h4>Adresse:</h4>';
	foreach ($resultadresse as $row1) {
		if($row1->strasse != "" && $row1->nr != ""){
		  $profileHTML .= $row1->strasse.' '.$row1->nr.'<br>';
		}
		//Bezirksnamen holen:
		$resultbezirk = db_select($tbl_bezirke, 'z')
		  ->fields('z', array(
		    'bezirksname',
		  ))
		  ->condition('BID', $row1->bezirk, '=')
		  ->execute();
		foreach ($resultbezirk as $row2) {
		  if($row1->plz != ""){
		    $profileHTML .= $row1->plz.' ';
		  }
		  if($row2->bezirksname != ""){
		    $profileHTML .= $row2->bezirksname;
		  }
		  $profileHTML .= '<br>';
		}
		if($row1->gps != ""){
		  $profileHTML .= 'GPS: '.$row1->gps.'<br>';
		}
	}
	}
	
	//Datum
	$profileHTML .= '<h4>Zeit:</h4>';
	if($row->start != "") { 
	  $explodedstart = explode(' ', $row->start);
	  $explodedende = explode(' ', $row->ende);
	  $profileHTML .= $explodedstart[0];
	  if($row->ende != $explodedstart[0]){
		$profileHTML .= '- '.$explodedende[0];
	  }
	  if($explodedstart[1] != "" && $explodedende[1] != ""){
	    $profileHTML .= '<br>'.$explodedstart[1].'-'.$explodedende[1].'<br>';
	  }
	}
	if($row->url != "") { $profileHTML .= '<br><a href="'.$row->url.'">'.$row->url.'</a><br>'; }
	if($row->kurzbeschreibung != "") { 
      $profileHTML .= '<h4>Beschreibung:</h4>';
	  $profileHTML .= $row->kurzbeschreibung.'<br>';
	}
	//Bild
	if($row->bild != "") { 
	  $profileHTML .= '<br><img src="'.$row->bild.'" >'; }
	
	//Sparten:
    if(count($sparten) != 0){
	  $profileHTML .= '<br>Sparten:<br>';
	  $laenge = count($sparten);
	  $j = 0;
	  while($j < $laenge){
	    $profileHTML .= '<p>'.$sparten[$j].'</p>';
	    $j = $j+1;
	  }
	}	
	
	if($okay == 1){
      $profileHTML .= '<br><a href="?q=Eventloeschen/'.$event_id.'" >'.Löschen.'</a>'.'   '.'<a href="?q=Eventedit/'.$event_id.'" >'.Bearbeiten.'</a>';
    }


}
