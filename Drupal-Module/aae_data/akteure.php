<?php
/**
 * akteure.php listet alle Akteure auf.
 *
 * Ruth, 2015-07-06
 * Felix, 2015-07-23
 */

//-----------------------------------

$tbl_akteur = "aae_data_akteur";

// Zeige wie viele Akteure pro Seite?
// TODO: Wert konfigurierbar machen via Filtermenü
$maxAkteure = '15';

//-----------------------------------

require_once $modulePath . '/database/db_connect.php';
$db = new DB_CONNECT();

// Paginator: Auf welcher Seite befinden wir uns?
$explodedPath = explode("/", current_path());
$currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

$itemsCount = db_query("SELECT COUNT(AID) AS count FROM ".$tbl_akteur)->fetchField();

// Paginator: Wie viele Seiten gibt es?

$maxPages = ceil($itemsCount / $maxAkteure);

if ($currentPageNr > $maxPages) {
  // Diese URL gibt es nicht, daher zurück...
  header("Location: ?q=Akteure/".$maxPages);
} elseif ($currentPageNr > 1) {
 $start = $maxAkteure * ($currentPageNr - 1);
 $ende = $maxAkteure * $currentPageNr;
} else {
 $start = 0;
 $ende = $maxAkteure;
}


//Auswahl aller Akteure (nur Name) in alphabetischer Reihenfolge
$resultAkteure = db_select($tbl_akteur, 'a')
  ->fields('a', array(
	'AID',
  'name',
  'beschreibung',
  'bild'
  ))
  ->orderBy('name', 'ASC')
  ->range($start, $ende) // Limit Query
  ->execute();

//-----------------------------------

ob_start(); // Aktiviert "Render"-modus

include_once path_to_theme().'/templates/akteure.tpl.php';

$profileHTML = ob_get_clean(); // Übergabe des gerenderten "project.tpl"
