<?php
/**
 * @file events.php
 * Listet alle Events auf.
 * Filterbar nach Datum, Tags, keywords, Bezirken und Zeitraum
 */

Class events extends aae_data_helper {

 var $presentationMode;
 var $getOldEvents;
 var $hasFilters = false;
 var $filter = array();
 var $filteredEventIds = array();
 var $filteredTags = array();
 var $filteredBezirke = array();
 var $numFilters = 0;

 public function run(){

  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) && $_GET['presentation'] == 'calendar') ? 'calendar' : 'timeline';

  if (isset($_GET['day']) && !empty($_GET['day'])) {
   $this->filter['day'] = $this->clearContent($_GET['day']);
  }

  if (isset($_GET['filterTags']) && !empty($_GET['filterTags'])) {
   $this->filter['tags'] = $_GET['filterTags'];
  }

  if (isset($_GET['filterKeyword']) && !empty($_GET['filterKeyword'])) {
   $this->filter['keyword'] = $this->clearContent($_GET['filterKeyword']);
  }

  if (isset($_GET['filterBezirke']) && !empty($_GET['filterBezirke'])) {
   $this->filter['bezirke'] = $_GET['filterBezirke'];
  }

  // Paginator: Auf welcher Seite befinden wir uns?
  $explodedPath = explode("/", $this->clearContent(current_path()));
  $currentPageNr = ($explodedPath[1] == '') ? '1' : $explodedPath[1];

  $this->getOldEvents = ($explodedPath[1] == 'old') ? true : false;

  //-----------------------------------

  $resultTags = $this->getAllTags('events');
  $resultBezirke = $this->getAllBezirke('events');

  // Filter nach Tags, falls gesetzt

  if (isset($this->filter['tags'])){

   $sparten = db_select($this->tbl_event_sparte, 'hs')
    ->fields('hs', array('hat_EID'));

   $and = db_and();

   foreach ($this->filter['tags'] as $tag) {

    $this->numFilters++;
    $tag = $this->clearContent($tag);
    $this->filteredTags[$tag] = $tag;
    $and->condition('hat_KID', $tag, '=');

   }

   $filterSparten = $sparten->condition($and)
    ->execute()
    ->fetchAll();

   foreach ($filterSparten as $sparte){
    $this->filteredEventIds[] = $sparte->hat_EID;
   }

  } // end Tag-Filter

  if (isset($this->filter['bezirke'])){

   foreach ($this->filter['bezirke'] as $bezirk) {

    $this->numFilters++;
    $bezirkId = $this->clearContent($bezirk);
    $this->filteredBezirke[$bezirkId] = $bezirkId;

    $adressen = db_select($this->tbl_adresse, 'a')
     ->fields('a', array('ADID'))
     ->condition('bezirk', $bezirkId, '=')
     ->execute()
     ->fetchAll();

    foreach ($adressen as $adresse) {
     $filterBezirke = db_select($this->tbl_event, 'e')
      ->fields('e', array('EID'))
      ->condition('ort', $adresse->ADID)
      ->execute()
      ->fetchAll();

     foreach ($filterBezirke as $bezirk) {
      $this->filteredEventIds[] = $bezirk->EID;
     }
    }
   }
  } // end Bezirke-Filter

  if (isset($this->filter['keyword'])) {

   $this->numFilters++;
   $or = db_or()
   ->condition('name', '%'.$this->filter['keyword'].'%', 'LIKE')
   ->condition('kurzbeschreibung', '%'.$this->filter['keyword'].'%', 'LIKE');

   $filterKeyword = db_select($this->tbl_event, 'e')
    ->fields('e', array('EID'))
    ->condition($or)
    ->execute()
    ->fetchAll();

   foreach ($filterKeyword as $keyword){
    $this->filteredEventIds[] = $keyword->EID;
   }

  } // end Keyword-Filter

  if (isset($this->filter['day'])) {

   $this->numFilters++;

   $resultDays = db_select($this->tbl_event, 'e')
    ->fields('e', array('EID'))
    ->condition('start_ts', $this->filter['day'].'%', 'LIKE')
    ->execute()
    ->fetchAll();

   foreach ($resultDays as $day){
    $this->filteredEventIds[] = $day->EID;
   }
  } // end Day-Filter

  // Get the actual results
  $this->filteredEventIds = $this->getDuplicates($this->filteredEventIds, $this->numFilters);

  $this->hasFilters = ($this->numFilters >= 1) ? true : false;

  // Auswahl aller Events in Reihenfolge ihres Starts
  $rEvents = db_select($this->tbl_event, 'a')
   ->fields('a')
   ->orderBy('start_ts', 'ASC');

  if ($this->getOldEvents) {

   $rEvents->where('DATE(start_ts) < CURDATE()');
   $rEvents->orderBy('start_ts', 'DESC');

  } else if (!$this->getOldEvents && !$this->hasFilters) {
   $rEvents->where('DATE(start_ts) >= CURDATE()');
  }

  if ($this->hasFilters && !empty($this->filteredEventIds)){

   $or = db_or();

   foreach ($this->filteredEventIds as $event){
    $or->condition('EID', $event);
   }

   $rEvents->condition($or);

 } else if ($this->hasFilters && empty($this->filteredEventIds)) {

   // No results :/
   $rEvents->condition('name', 'assdf55asdf216we');

 }

  $resultEvents = $rEvents->execute()->fetchAll();

  $counter = 0;

  if ($this->presentationMode == 'calendar') {

   $modulePath = drupal_get_path('module', 'aae_data');
   include_once $modulePath . '/kalender.php';

   $kal = new kalender();
   $resultKalender = $kal->show();

  } else {

  // Add specific data from other tables... we don't need no joins, yah'

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
     ->fetchObject();

    $resultAkteur = db_select($this->tbl_akteur, 'a')
     ->fields('a',array('AID','name','bild'))
     ->condition('AID', $akteurId->AID)
     ->execute()
     ->fetchAll();

   // Hack: add variable to $resultEvents-object
   $resultEvents[$counter] = (array)$resultEvents[$counter];
   $resultEvents[$counter]['tags'] = $sparten;
   $resultEvents[$counter]['akteur'] = $resultAkteur;
   $resultEvents[$counter]['start'] = new DateTime($event->start_ts);
   $resultEvents[$counter]['ende'] = new DateTime($event->ende_ts);
   $resultEvents[$counter] = (object)$resultEvents[$counter];

   $counter++;

   }
  }

  $resultTagCloud = db_query_range('SELECT COUNT(*) AS count, s.KID, s.kategorie FROM {aae_data_sparte} s INNER JOIN {aae_data_event_hat_sparte} hs ON s.KID = hs.hat_KID GROUP BY hs.hat_KID HAVING COUNT(*) > 0 ORDER BY count DESC', 0, 8);

  $itemsCount = db_query("SELECT COUNT(EID) AS count FROM " . $this->tbl_event)->fetchField();


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
    ->orderBy('start_ts', 'ASC')
    ->where('DATE(start_ts) >= CURDATE()')
    ->execute()
    ->fetchAll();

   ob_start();
   include_once path_to_theme() . '/templates/events.rss.tpl.php';
   exit();

 } // end function rss()

} // end class events
