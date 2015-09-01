<?php
/**
 * events.php listet alle Events auf.
 *
 * Ruth, 2015-07-10
 * Felix, 2015-09-01
 * TODO: Einrichtung des Paginators, s. akteure.php
 * TODO: Vereinheitlichung des Filter abrufens
 */

//-----------------------------------

$tbl_events = "aae_data_event";
$tbl_event_tags = "aae_data_event_hat_sparte";
$tbl_tags = "aae_data_kategorie";

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

$pathThisFile = $_SERVER['REQUEST_URI'];

$resulttags = db_select($tbl_tags, 't')
  ->fields('t', array(
    'KID',
	'kategorie',
  ))
  ->execute();
$counttags = $resulttags->rowCount();

//-----------------------------------
if (isset($_POST['submit'])) {
  $tag = $_POST['tag'];
  if($tag != 0){
    //Auswahl der Events mit entsprechendem Tag in alphabetischer Reihenfolge
    $result = db_select($tbl_event_tags, 't');
    $result->join($tbl_events, 'e', 't.hat_EID = e.EID AND t.hat_KID = :kid', array(':kid' => $tag));
    $result->fields('e', array('name', 'EID', 'kurzbeschreibung', 'start'))->orderBy('name', 'ASC');
    $resultevents = $result->execute();
  }else{
	//Auswahl aller Events in alphabetischer Reihenfolge
    $resultevents = db_select($tbl_events, 'a')
    ->fields('a', array(
      'name',
      'EID',
	  'kurzbeschreibung',
	  'start',
	))
    ->orderBy('name', 'ASC')
    ->execute();
  }
} else {

  // Auswahl aller Events in alphabetischer Reihenfolge

  $resultevents = db_select($tbl_events, 'a')
    ->fields('a', array(
      'name',
      'EID',
	  'kurzbeschreibung',
	  'start',
    ))
    ->orderBy('start', 'ASC')
    ->execute();
}

// Ausgabe der Events

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/events.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "events.tpl"
