<?php
/**
 * akteure.php listet alle Akteure auf.
 */

Class akteure extends aae_data_helper {

 public function run(){

 $presentationMode = "boxen"; // available: "boxen" & "map"
 // Zeige wie viele Akteure pro Seite?
 // TODO: Wert konfigurierbar machen via Filtermenü
 $maxAkteure = '15';

 //-----------------------------------

 // Paginator: Auf welcher Seite befinden wir uns?
 $explodedPath = explode("/", current_path());
 $currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

 $itemsCount = db_query("SELECT COUNT(AID) AS count FROM " . $this->tbl_akteur)->fetchField();

 // Paginator: Wie viele Seiten gibt es?
 $maxPages = ceil($itemsCount / $maxAkteure);

 if ($currentPageNr > $maxPages) {
 // Diese URL gibt es nicht, daher zurueck...
  header("Location: Akteure/" . $maxPages);
 } elseif ($currentPageNr > 1) {
  $start = $maxAkteure * ($currentPageNr - 1);
  $ende = $maxAkteure * $currentPageNr;
 } else {
  $start = 0;
  $ende = $maxAkteure;
 }

//-----------------------------------

if (isset($_GET['submit'])) $presentationMode = $_GET['presentation'];
// -> Clear with ClearContent()

//-----------------------------------

// Auswahl aller Akteure in alphabetischer Reihenfolge
$resultAkteure = db_select($this->tbl_akteur, 'a')
  ->fields('a', array(
	'AID',
  'name',
  'beschreibung',
  'bild',
  ))
  ->orderBy('name', 'ASC')
  ->range($start, $ende)
  ->execute();

  $resulttags = db_select($this->tbl_sparte, 't')
   ->fields('t', array(
   'KID',
   'kategorie',
   ))
  ->execute();

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme().'/templates/akteure.tpl.php';
  return ob_get_clean(); // Uebergabe des gerenderten Template's

 }
} // end class akteure
?>
