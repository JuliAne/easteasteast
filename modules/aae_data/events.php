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
 var $tag;
 var $dateint;

 public function run(){

  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) ? $this->clearContent($_GET['presentation']) : 'timeline');
  // Available: "timeline"[default] & "kalender"
  $this->tag = (isset($_GET['day']) && !empty($_GET['day']) ? $this->clearContent($_GET['day']) : '');


  // TODO: limit

  $this->maxEvents = '16';

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
  $resultBezirke = $this->getAllBezirke();

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

    $kal = new kalender(true);
    $resultKalender = $kal->show();

  }

  if (!empty($this->tag)) {

    // DB-Abfrage aller Events, die an diesem Tag stattfinden

    $resultEvents = db_select($this->tbl_event, 'e')
     ->fields('e')
     ->condition('start', $this->tag.'%', 'LIKE')
     ->orderBy('name', 'ASC')
     ->execute()
     ->fetchAll();

    //Temporary Workaround to sort events by date:
    foreach ($resultEvents as $event) {
    // 1 Split strings by year, month, date
    // 2 Merge single strings for year, month, date together ($datesum)
    // 3 sort array reultsEvents by $datesum
    $datestringstart = preg_replace("/[^0-9]/", "", $event->start);
    $datestring = preg_replace("/[^0-9]/", "", $event->start);
    $datestringyear = substr($datestringstart, 0, 4);
    $datestringten = substr($datestring, 0, 8);

    $datestringyearhalf = (int) substr($datestringyear, 2, 4);

    if($datestringyearhalf < 13) {
      $datestringyear = substr($datestringten, 4, 4);
      $datestringmonth = substr($datestringten, 2,2);
      $datestringday = substr($datestringten, 0,2);
    } else {
      $datestringyear = $datestringyear;
      $datestringmonth = substr($datestringten, 4,2);
      $datestringday = substr($datestringten, 6,2);
    }

    $datesum = "{$datestringyear}{$datestringmonth}{$datestringday}";

    // Hack: add variable to $resultEvents-object
    $event->datestringstart = $datestringten;
    $event->dateyear = $datestringyear;
    $event->datemonth = $datestringmonth;
    $event->dateday = $datestringday;
    $event->datesum = (int) $datesum;
    }

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

  $resultEvents = $rEvents
   ->execute()
   ->fetchAll();

   $counter = 0;

   foreach ($resultEvents as $event) {

     //Selektion der Tags
     $resultSparten = db_select($this->tbl_event_sparte, 's')
      ->fields('s', array( 'hat_KID' ))
      ->condition('hat_EID', $event->EID, '=')
      ->execute();

     $countSparten = $resultSparten->rowCount();
     $sparten = array();

     if ($countSparten != 0) {

      foreach ($resultSparten as $row) {
       $resultSpartenName = db_select($this->tbl_sparte, 'sp')
       ->fields('sp')
       ->condition('KID', $row->hat_KID, '=')
       ->execute();

       foreach ($resultSpartenName as $row1) {
        $sparten[] = $row1;
       }
      }
     }

     $akteurId = db_select($this->tbl_akteur_events, 'ae')
      ->fields('ae', array('AID'))
      ->condition('EID', $event->EID, '=')
      ->execute()
      ->fetchAssoc();

     $resultAkteur = db_select($this->tbl_akteur, 'a')
      ->fields('a',array('AID','name','bild'))
      ->condition('AID', $akteurId['AID'], '=')
      ->execute()
      ->fetchAll();


    //Workaround to sort events by date:
    // 1 Split strings by year, month, date
    // 2 Merge single strings for year, month, date together ($datesum)
    // 3 sort array reultsEvents by $datesum
    $datestringstart = preg_replace("/[^0-9]/", "", $event->start);
    $datestring = preg_replace("/[^0-9]/", "", $event->start);
    $datestringyear = substr($datestringstart, 0, 4);
    $datestringten = substr($datestring, 0, 8);

    $datestringyearhalf = (int) substr($datestringyear, 2, 4);

    if($datestringyearhalf < 13) {
      $datestringyear = substr($datestringten, 4, 4);
      $datestringmonth = substr($datestringten, 2,2);
      $datestringday = substr($datestringten, 0,2);
    } else {
      $datestringyear = $datestringyear;
      $datestringmonth = substr($datestringten, 4,2);
      $datestringday = substr($datestringten, 6,2);
    }

    $datesum = "{$datestringyear}{$datestringmonth}{$datestringday}";

    // Hack: add variable to $resultEvents-object
    $resultEvents[$counter] = (array)$resultEvents[$counter];
    $resultEvents[$counter]['tags'] = $sparten;
    $resultEvents[$counter]['akteur'] = $resultAkteur;
    $resultEvents[$counter]['datestringstart'] = $datestringten;
    $resultEvents[$counter]['dateyear'] = $datestringyear;
    $resultEvents[$counter]['datemonth'] = $datestringmonth;
    $resultEvents[$counter]['dateday'] = $datestringday;
    //$resultEvents[$counter]['dateinthalf'] = $datestringyearhalf;
    $resultEvents[$counter]['datesum'] = (int) $datesum;
    $resultEvents[$counter] = (object)$resultEvents[$counter];

    $counter++;

    }

  }

  // OLD SORTING FUNCTION
  // function sortEvents($a,$b){
  //  $a = intval(strrev(str_replace("-","",$a->start)));
  //  $b = intval(strrev(str_replace("-","",$b->start)));
  //  if ($a == $b) return 0;
  //  else if ($a < $b) return -1;
  //  else return 1;
  // }
  // usort($resultEvents, 'sortEvents');


  // NEW SORTING FUNCTION (Juliane, 15.01.2016)
  // sort array $resultEvents by datesum
  foreach ($resultEvents as $event) {
    $datesort[] = $event->datesum;
  }
  array_multisort($datesort, SORT_DESC, $resultEvents);
  $resultEventsBool = array_multisort($datesort, SORT_DESC, $resultEvents);
  // END SORTING FUNCTION

  // Ausgabe der Events
  ob_start(); // Aktiviert "Render"-modus
  include_once path_to_theme() . '/templates/events.tpl.php';
  return ob_get_clean(); // Übergabe des gerenderten "events.tpl"


 } // end function run()

 public function rss(){

   header("Content-type: text/xml");
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

   $resultEvents = db_select($this->tbl_event, 'e')
    ->fields('e')
    ->execute()
    ->fetchAll();

  // sortieren

   ob_start(); // Aktiviert "Render"-modus
   include_once path_to_theme() . '/templates/events.rss.tpl.php';
   exit();

 }
} // end class events
