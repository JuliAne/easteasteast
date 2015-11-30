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

  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'timeline');
  // Available: "timeline"[default] & "kalender"

  // TODO: limit

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

  $resultTags = $this->getAllTags();

  // Filter nach Tags, falls gesetzt

  $filterTags = array();

  if (isset($_GET['tags']) && !empty($_GET['tags'])){

     $fSparten = db_select($this->tbl_event_sparte, 'hs')
     ->fields('hs', array('hat_EID'));

     $and = db_and();

     foreach($_GET['tags'] as $tag) {

      $tag = $this->clearContent($tag);
      $filterTags[$tag] = $tag;
      $and->condition('hat_KID', $tag, '=');

     }

     $filterSparten = $fSparten->condition($and)
      ->execute()
      ->fetchAssoc();

     array_unique($filterSparten); // Lösche doppelte Einträge

  }

  if ($this->presentationMode == 'kalender') {

    $modulePath = drupal_get_path('module', 'aae_data');
    include_once $modulePath . '/kalender.php';

    $kal = new kalender();
    $resultKalender = $kal->show();

  } else {

   // Auswahl aller Events in alphabetischer Reihenfolge
   $rEvents = db_select($this->tbl_event, 'a')
    ->fields('a');

   if (isset($filterSparten) && !empty($filterSparten)) {

     $or = db_or();

     foreach ($filterSparten as $id => $sparte) {
       $or->condition('EID', $sparte, '=');
     }

     $rEvents->condition($or);

  } else if (isset($filterSparten) && empty($filterSparten)) {

      // Keine Akteure mit entsprechendem Tag gefunden, daher negatives resultEvents

      $rEvents->condition('name', 'xlyjdflafdSDas', '=');

  }

  $resultEvents = $rEvents->orderBy('start', 'ASC')
   #->range()
   ->execute()
   ->fetchAll();

  }

  // Ausgabe der Events
  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/events.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "events.tpl"

 }
} // end class events
