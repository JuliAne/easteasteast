<?php
/**
 * @file eventspage.php
 *
 * Listet alle Events als Timeline oder Kalender auf.
 * Auch als Block für Festivals einsetzbar (Filterung erfolgt automatisch).  
 * Filterbar (via Model) nach Datum, Tags, keywords, Bezirken und Zeitraum
 * 
 * TODO: Die Filter-Sachen sind zu prozedural und buggy. Am besten stilvoll in 
 *       Model-Methoden auslagern (model->setTimespan / ->setOrder)
 */

namespace Drupal\AaeData;

Class eventspage extends aae_data_helper {

 var $presentationMode;
 var $getOldEvents = false;
 var $hasFilters = false;
 var $isBlock;
 var $filter = array();
 
 public function __construct($isBlock = false){
   
  parent::__construct();

  $this->events  = new events();
  $this->tags    = new tags();
  $this->isBlock = $isBlock;
  
 }

 public function run(){

  if (!empty($_GET['presentation']) && $_GET['presentation'] == 'calendar'){
   $this->presentationMode = 'calendar';
  } else {
   $this->presentationMode = 'timeline';
  }

  $orderBy = 'ASC';
  $start = NULL;

  if (isset($_GET['day']) && !empty($_GET['day'])) {
   $this->filter['day'] = $this->clearContent($_GET['day']);
  }

  if (isset($_GET['filterTags']) && !empty($_GET['filterTags'])) {
   $this->filter['tags'] = $_GET['filterTags']; # Becomes escaped in model
  }

  if (isset($_GET['filterKeyword']) && !empty($_GET['filterKeyword'])) {
   $this->filter['keyword'] = $this->clearContent($_GET['filterKeyword']);
  }

  if (isset($_GET['filterBezirke']) && !empty($_GET['filterBezirke'])) {
   $this->filter['bezirke'] = $_GET['filterBezirke']; # Becomes escaped in model
  }

  if (isset($_GET['AID']) && !empty($_GET['AID'])){
   $this->allowDuplicates = true;
   $this->filter['AID'] = $_GET['AID'];
  }

  if (isset($_GET['UID']) && $_GET['UID'] == $this->user_id){
   $this->allowDuplicates = true;
   $this->filter['UID'] = $_GET['UID'];
  }
  
  if (isset($_GET['timespan']) && !empty($_GET['timespan'])) {
   # Input: ...?timespan=02.16-05.16
   $timespan = explode('-', $this->clearContent($_GET['timespan']));
   $begin = explode('.', $timespan[0]);
   $end = explode('.', $timespan[1]);
   
   $start = array(
    '0' => array(
     'date' => (new \DateTime($begin[1].'-'.$begin[0].'-01'))->format('Y-m-d 00:00:00'),
     'operator' => '>='
    ),
    '1' => array(
     'date' => (new \DateTime(date('Y-m-t', mktime(0, 0, 0, $end[0]-1, 1, $end[1]))))->format('Y-m-d 23:59:59'),
     'operator' => '<='
    )
   );
   
  }

  if (!empty($this->filter))
    $orderBy = 'DESC';

  // Paginator (BIG TODO - will be replaced by AJAX-calls)
  $explodedPath = explode('/', $this->clearContent(current_path()));
  $currentPageNr = ($explodedPath[1] == '') ? '1' : $explodedPath[1];
  
  if (empty($start) && empty($this->filter)) {
  
   if ($explodedPath[1] == 'old') {
     
    $start = array(
     '0' => array(
      'date' => (new \DateTime(date()))->format('Y-m-d 00:00:00'),
      'operator' => '<'
     )
    );
    
    $orderBy = 'DESC';
    $this->getOldEvents = 1;
    
    drupal_set_title(t('Vergangene Events'));
  
  } else {
  
    $start = array(
     '0' => array(
      'date' => (new \DateTime(date()))->format('Y-m-d 00:00:00'),
      'operator' => '>='
     )
    );

   }
   
  }

  //-----------------------------------

  $resultTags = $this->tags->getTags('events');
  $resultBezirke = $this->getAllBezirke('events');
  
  if (!empty($this->filter)) {
   $resultEvents = $this->events->getEvents(array('filter' => $this->filter, 'start' => $start), ($this->presentationMode == 'calendar' ? 'EID' : 'normal'), false, $orderBy);
  } else if ($this->isBlock) {
   $resultEvents = $this->events->getEvents(array('FID' => $_SESSION['fid']), ($this->presentationMode == 'calendar' ? 'EID' : 'complete'), false, $orderBy);
   unset($_SESSION['fid']); # 2b improved soon
  } else if ($this->presentationMode != 'calendar')  {

    if ($this->getOldEvents) {
      // Workaround to save some performance for getOldEvents-Mode... should be implemented with paginator-functionality
      $resultEvents = $this->events->getEvents(array('limit' => 50, 'start' => $start), 'complete', false, $orderBy);
    } else {
      $resultEvents = $this->events->getEvents(array('start' => $start), 'complete', false, $orderBy);
    }
  }

  if ($this->presentationMode == 'calendar') {

   include_once $this->modulePath . '/kalender.php';
   
   // New: We allow calendar-results to become filtered as well...
   $kal = new kalender((isset($resultEvents) ? (empty($resultEvents) ? 'empty' : $resultEvents) : null));
   $resultKalender = $kal->show();

  } /*else if ($this->presentationMode == 'map'){
  
   // MAY BE USED IN FUTURE TIMES TO GENERATE new/interesting spots in location and send them by mail
   if (empty($_GET['geodata'] || !isset($_GET['geodata']))) {
    break;
   }

   $userLocation = explode(',', $this->clearContent($_GET['geodata']));
   $collectedADIDs = array();
   $distance = 2; // Distance-radius in km

   print_r($userLocation);

   $resultLocations = db_query('SELECT `ADID`, (6371 * acos( cos( radians( :lat) ) * cos( radians( `gps_lat` ) ) * cos( radians( `gps_long` ) - radians(:lng) ) + sin( radians(:lat) ) * sin( radians( `gps_lat` ) ) ) ) AS distance
    FROM `aae_data_adresse` HAVING distance <= :distance
    ORDER BY distance ASC', array(':distance' => $distance, ':lat' => $userLocation[0] , ':lng' => $userLocation[1] ))->fetchAll();

   echo '<br />'.count($resultLocations);

   foreach ($resultLocations as $location){
    $collectedADIDs[$location->ADID] = $location->ADID;
   }

print_r($collectedADIDs);
   #print_r($resultLocations);

   $resultEventsInRadius = $this->events->getEvents(array('start' => $start, 'ort' => $collectedADIDs), 'complete');
   
   print_r($resultEventsInRadius);
   exit();

   $js = 'var addressPoints = [';

   foreach ($resultLocations as $location) {

    if (!empty($akteur->gps)) {
     $beschreibung = (!empty($akteur->kurzbeschreibung)) ? ' - '.$akteur->kurzbeschreibung.'...' : '';
     $js .= '['.$akteur->gps.',"<a href=\''.base_path().'akteurprofil/'.$akteur->AID.'\'>'.$akteur->name.'</a>'.strip_tags($beschreibung, '<p>').'"],';
    }

   }

   $js .= '];';
   drupal_add_js($js, 'inline');
   // Needed to add Map-Files:
   $this->addMapContent('','',array('something' => 'bla'));
   

  } */

  $resultTagCloud = db_query_range('SELECT COUNT(*) AS count, s.KID, s.kategorie FROM {aae_data_sparte} s INNER JOIN {aae_data_event_hat_sparte} hs ON s.KID = hs.hat_KID GROUP BY hs.hat_KID HAVING COUNT(*) > 0 ORDER BY count DESC', 0, 10);
  $itemsCount = db_query("SELECT COUNT(EID) AS count FROM " . $this->tbl_event)->fetchField();
  
  $festivals = db_select($this->tbl_festival, 'f')
    ->fields('f', array('name','alias'))
    ->execute()
    ->fetchAll();

  // Ausgabe der Events
  ob_start(); // Aktiviert "Render"-modus
  
  if ($this->isBlock) {

   // TODO: Interact with festivals-class
    
   drupal_add_js($this->themePath.'/js/CountUp.js');
   include_once $this->themePath . '/templates/festival_events_block.tpl.php';
   echo ob_get_clean();
   
  } else {
    
   include_once path_to_theme() . '/templates/events.tpl.php';
   return ob_get_clean(); // Übergabe des gerenderten "events.tpl"
  
  }
 } // end function run()

 public function rss(){

   // TODO: Add params to generate events for akteur/festival only

   header("Content-type: text/xml");
   echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

    $start = array(
     '0' => array(
      'date' => (new \DateTime(date()))->format('Y-m-d 00:00:00'),
      'operator' => '>='
     )
    );

   $resultEvents = $this->events->getEvents(array('start' => $start));

   ob_start();
   include_once path_to_theme() . '/templates/events.rss.tpl.php';
   drupal_exit();

 } // end function rss()
} // end class events
