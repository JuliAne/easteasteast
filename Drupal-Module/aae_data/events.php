<?php
/**
 * events.php listet alle Events auf.
 *
 * Ruth, 2015-07-10
 * Felix, 2015-09-01
 * TODO: Vereinheitlichung des Filter abrufens bzw. Anpassung des
 * Paginator's an (bedingt durch Filter) veränderte Eventanzahlen
 */

//-----------------------------------

$tbl_events = "aae_data_event";
$tbl_event_tags = "aae_data_event_hat_sparte";
$tbl_tags = "aae_data_kategorie";

// Zeige wie viele Events pro Seite?
// TODO: Wert konfigurierbar machen via Filtermenü (Darstellung: 15 - 25 - Alle)
$maxEvents = '15';

//-----------------------------------

// Paginator: Auf welcher Seite befinden wir uns?
$explodedPath = explode("/", current_path());
$currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

$itemsCount = db_query("SELECT COUNT(EID) AS count FROM " . $tbl_events)->fetchField();

// Paginator: Wie viele Seiten gibt es?
$maxPages = ceil($itemsCount / $maxEvents);

if ($currentPageNr > $maxPages) {
  // Diese URL gibt es nicht, daher zurück...
  header("Location: Events/" . $maxPages);
} elseif ($currentPageNr > 1) {
  $start = $maxEvents * ($currentPageNr - 1);
  $ende = $maxEvents * $currentPageNr;
} else {
  $start = 0;
  $ende = $maxEvents;
}

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
  if ($tag != 0) {
    //Auswahl der Events mit entsprechendem Tag in alphabetischer Reihenfolge
    $result = db_select($tbl_event_tags, 't');
    $result->join($tbl_events, 'e', 't.hat_EID = e.EID AND t.hat_KID = :kid', array(':kid' => $tag));
    $result->fields('e', array('name', 'EID', 'kurzbeschreibung', 'start'))->orderBy('name', 'ASC');
    $resultevents = $result->execute();
  } else {
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
include_once path_to_theme() . '/templates/events.tpl.php';
$profileHTML = ob_get_clean(); // Übergabe des gerenderten "events.tpl"
