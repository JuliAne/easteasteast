<?php
/**
 * akteure.php listet alle Akteure auf.
 */

Class akteure extends aae_data_helper {

 var $presentationMode;
 var $maxAkteure;
 var $sparten;

 public function run(){

 $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'boxen');
 // available actions: "boxen" & "map"

 // Zeige wie viele Akteure pro Seite?
 $this->maxAkteure = (isset($_GET['display_number']) && !empty($_GET['display_number']) ? $this->clearContent($_GET['display_number']) : '15' );

 //-----------------------------------

 // Paginator: Auf welcher Seite befinden wir uns?
 $explodedPath = explode("/", current_path());
 $currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

 $itemsCount = db_query("SELECT COUNT(AID) AS count FROM " . $this->tbl_akteur)->fetchField();

 // Paginator: Wie viele Seiten gibt es?
 $maxPages = ceil($itemsCount / $this->maxAkteure);

 if ($currentPageNr > $maxPages) {
 // Diese URL gibt es nicht, daher zurueck...
  header("Location: Akteure/" . $maxPages);
 } elseif ($currentPageNr > 1) {
  $start = $this->maxAkteure * ($currentPageNr - 1);
  $ende = $this->maxAkteure * $currentPageNr;
 } else {
  $start = 0;
  $ende = $this->maxAkteure;
 }

//-----------------------------------

if (isset($_GET['tags']) && !empty($_GET['tags'])){

  $filterSparten = db_select($this->tbl_hat_sparte, 'hs')
   ->fields('hs');

   $db_or = db_or();

foreach($_GET['tags'] as $tag) {

  $tag = $this->clearContent($tag);
  $db_or->condition('hat_KID', $tag, '=');

 }

 $filterSparten->condition($db_or)
  ->execute()
  ->fetchAssoc();

  //print_r($filterSparten);

 foreach($filterSparten as $sparte) {

 }

}

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
