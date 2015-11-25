<?php
/**
 * events.php listet alle Events auf.
 *
 * TODO: Vereinheitlichung des Filter abrufens bzw. Anpassung des
 * Paginator's an (bedingt durch Filter) veränderte Eventanzahlen
 */

Class events extends aae_data_helper {

 var $presentationMode;
 var $maxEvents;
 var $sparten;

 public function run(){

  $this->maxEvents = '15';

  // Paginator: Auf welcher Seite befinden wir uns?
  $explodedPath = explode("/", $this->clearContent(current_path()));
  $currentPageNr = ($explodedPath[1] == '' ? '1' : $explodedPath[1]);

  $itemsCount = db_query("SELECT COUNT(EID) AS count FROM " . $this->tbl_event)->fetchField();

  // Paginator: Wie viele Seiten gibt es?
  $maxPages = ceil($itemsCount / $this->maxEvents);

  if ($currentPageNr > $maxPages) {
   // Diese URL gibt es nicht, daher zurück...
   header("Location: Events/" . $maxPages);
  } else if ($currentPageNr > 1) {
   $start = $this->maxEvents * ($currentPageNr - 1);
   $ende = $this->maxEvents * $currentPageNr;
  } else {
   $start = 0;
   $ende = $this->maxEvents;
  }

  //-----------------------------------

  $pathThisFile = $_SERVER['REQUEST_URI'];

  $resulttags = $this->getAllTags();

  $counttags = $resulttags->rowCount();


  if (isset($_POST['submit'])) {

   // FUNKTIONIERT DIES?

   $tag = $this->clearContent($_POST['tags']);

   if ($tag != 0) {

    //Auswahl der Events mit entsprechendem Tag in alphabetischer Reihenfolge
    $result = db_select($this->tbl_event_sparte, 't');
    $result->join($tbl_event, 'e', 't.hat_EID = e.EID AND t.hat_KID = :kid', array(':kid' => $tag));
    $result->fields('e', array('name', 'EID', 'kurzbeschreibung', 'start'))->orderBy('name', 'ASC');
    $resultevents = $result->execute();

   } else {

    //Auswahl aller Events in alphabetischer Reihenfolge
    $resultevents = db_select($this->tbl_event, 'a')
    ->fields('a')
    ->orderBy('name', 'ASC')
    ->execute();
  }
} else {

   // Auswahl aller Events in alphabetischer Reihenfolge
   $resultevents = db_select($this->tbl_event, 'a')
    ->fields('a')
    ->orderBy('start', 'ASC')
    ->execute();
 }

  // Ausgabe der Events
  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/events.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "events.tpl"

 }
} // end class events
