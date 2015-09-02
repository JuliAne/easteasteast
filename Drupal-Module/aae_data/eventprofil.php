<?php
/**
 * eventprofil.php zeigt das Profil eines Events an.
 *
 * Ruth, 2015-07-10
 * Felix, 2015-09-01
 * TODO (Felix): Hier scheint einiges durch den Einsatz von DB-Join's vereinfachbar,
 *       siehe etwa "Sparten-Query"...
 *       Vlt. kann das mal jemand für sich später auf die TODO-Liste setzen?
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
$eventId = $explodedpath[1];

//Prüfen, wer Schreibrechte hat
//Sicherheitsschutz, ob User entsprechende Rechte hat

$resultAkteurId = db_select($tbl_akteur_events, 'e')
  ->fields('e', array( 'AID' ))
  ->condition('EID', $eventId, '=')
  ->execute();

$akteurId = "";
$okay = ""; // Gibt an, ob Zugang erlaubt wird oder nicht

foreach ($resultAkteurId as $row) {

  $akteurId = $row->AID; //Akteur speichern

  //Prüfen ob Schreibrecht vorliegt: ob User zu dem Akteur gehört
  $resultUser = db_select($tbl_hat_user, 'u')
    ->fields('u', array(
      'hat_UID',
      'hat_AID',
    ))
    ->condition('hat_AID', $akteurId, '=')
    ->condition('hat_UID', $user->uid, '=')
    ->execute();

  if($resultUser->rowCount() == 1) $okay = 1; //Zugang erlaubt
}

//Abfrage, ob User Ersteller des Events ist:
$ersteller = db_select($tbl_event, 'e')
  ->fields('e', array( 'ersteller' ))
  ->condition('ersteller', $user->uid, '=')
  ->execute();

 if($ersteller->rowCount() == 1) $okay = 1;

 if(array_intersect(array('administrator'), $user->roles)){
  $okay = 1;
 }

//Selektion der Eventinformationen

$resultEvent = db_select($tbl_event, 'a')
  ->fields('a')
  ->condition('EID', $eventId, '=')
  ->execute()
  ->fetchAll();

 foreach($resultEvent as $event) {
  $resultEvent = $event; // Kleiner Fix, um EIN Objekt zu generieren
 }

$resultVeranstalter = db_select($tbl_akteur_events, 'e');
$resultVeranstalter->join($tbl_akteur, 'a');
$resultVeranstalter
  ->fields('e')
  ->condition('e.EID', $eventId, '=')
  ->condition('a.AID', $akteurId, '=')
  ->execute()
  ->fetchAll();


/* VOR DEM JOIN SAH DAS GANZE SO AUS:

  $resultAkteur = db_select($tbl_akteur, 'b')
  ->fields('b', array(
    'name',
  ))
  ->condition('AID', $row1->AID, '=')


$query = db_select('node', 'n');
$query->join('field_data_body', 'b', 'n.nid = b.entity_id');
$query
  ->fields('n', array('nid', 'title'))
  ->condition('n.type', 'page')
  ->condition('n.status', '1')
  ->orderBy('n.created', 'DESC') */

foreach($resultVeranstalter as $veranstalter) {
 $resultVeranstalter = $veranstalter; // Kleiner Fix, um EIN Objekt zu generieren
}

//Selektion der Tags
$resultSparten = db_select($tbl_event_sparte, 's')
  ->fields('s', array( 'hat_KID' ))
  ->condition('hat_EID', $eventId, '=')
  ->execute();

$countSparten = $resultSparten->rowCount();
$sparten = array();
$i = 0;

if($countSparten != 0){
	foreach ($resultSparten as $row) {
	  $resultSpartenName = db_select($tbl_sparte, 'p')
	  ->fields('p', array(
	    'kategorie',
	  ))
	  ->condition('KID', $row->hat_KID, '=')
	  ->execute();
	  foreach ($resultSpartenName as $row1) {
	  	$sparten[$i] = $row1->kategorie;
	  }
	  $i++;
	}
}

//Ersteller (USER!) aus DB holen

$ersteller = db_select("users", 'u')
->fields('u', array(
  'name',
))
->condition('uid', $resultEvent->ersteller, '=')
->execute();

//Adresse des Akteurs

$resultAdresse = db_select($tbl_adresse, 'b')
  ->fields('b', array(
    'strasse',
    'nr',
    'plz',
    'bezirk',
    'gps',
  ))
  ->condition('ADID', $resultEvent->ort, '=')
  ->execute();

  foreach($resultAdresse as $adresse) {
   $resultAdresse = $adresse; // Kleiner Fix, um EIN Objekt zu generieren
  }

//Bezirksnamen

 $resultBezirk = db_select($tbl_bezirke, 'z')
 ->fields('z', array( 'bezirksname' ))
 ->condition('BID', $resultAdresse->bezirk, '=')
 ->execute();

 foreach($resultBezirk as $bezirk) {
  $resultBezirk = $bezirk; // Kleiner Fix, um EIN Objekt zu generieren
 }

// Ausgabe des Events

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/single_event.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "events.tpl.php"
