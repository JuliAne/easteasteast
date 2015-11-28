<?php
/**
 * akteure.php listet alle Akteure auf.
 */

Class akteure extends aae_data_helper {

 var $presentationMode;
 var $maxAkteure;
 var $sparten;

 public function run(){

 // Verfügbare actions: "boxen"[default] & "map"
 $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'boxen');

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
   ->fields('hs', array('hat_AID'));

   $ore = db_or();

 foreach($_GET['tags'] as $tag) {

  $tag = $this->clearContent($tag);
  $ore->condition('hat_KID', $tag);

 }

 $filterSparten->condition($ore)
  ->execute()
  ->fetchAssoc();

 //print_r($filterSparten);

 foreach(array_unique($filterSparten) as $sparte) {
  print_r($sparte);
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
  'adresse'
  ))
  ->orderBy('name', 'ASC') // TODO: Nach neuesten filtern
  ->range($start, $ende)
  ->execute()
  ->fetchAll();

  // Get Bezirk
  foreach ($resultAkteure as $counter => $akteur) {

   $adresse = db_select($this->tbl_adresse, 'ad')
    ->fields('ad', array('bezirk'))
    ->condition('ADID', $akteur->adresse, '=')
    ->execute()
    ->fetchAssoc();

   $bezirk = db_select($this->tbl_bezirke, 'b')
    ->fields('b')
    ->condition('BID', $adresse['bezirk'], '=')
    ->execute()
    ->fetchAssoc();

   // Add variable to $resultAkteure
   $resultAkteure[$counter] = (array)$resultAkteure[$counter];
   $resultAkteure[$counter]['bezirk'] = $bezirk['bezirksname'];
   $resultAkteure[$counter] = (object)$resultAkteure[$counter];

  }

  if ($this->presentationMode == 'map') {
   $this->addMapContent('', '', array('file' => base_path().drupal_get_path('module', 'aae_data').'/LOdata.js'));
  }

  $resulttags = $this->getAllTags();

  // TODO return $this->render();

  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme().'/templates/akteure.tpl.php';
  return ob_get_clean(); // Uebergabe des gerenderten Template's

 }
} // end class akteure
?>
