<?php
/**
 * @file eventspage.php
 * Listet alle Events auf.
 * Filterbar (via Model) nach Datum, Tags, keywords, Bezirken und Zeitraum
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
  
  require_once('models/events.php');
  $this->events = new events();
  $this->isBlock = $isBlock;
  
 }

 public function run(){

  $this->presentationMode = (isset($_GET['presentation']) && !empty($_GET['presentation']) && $_GET['presentation'] == 'calendar') ? 'calendar' : 'timeline';

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
  
  if (isset($_GET['timespan']) && !empty($_GET['timespan'])) {
   # Input: ...?timespan=02.16-05.16
   $timespan = explode('-',$this->clearContent($_GET['timespan']));
   $begin = explode('.',$timespan[0]);
   $end = explode('.',$timespan[1]);
   
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

  // Paginator (will be replaced by AJAX-calls)
  $explodedPath = explode("/", $this->clearContent(current_path()));
  $currentPageNr = ($explodedPath[1] == '') ? '1' : $explodedPath[1];
  $orderBy = 'ASC';
  
  if (!isset($start)) {
  
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

  $resultTags = $this->events->getTags();
  $resultBezirke = $this->getAllBezirke('events');
  
  if (!empty($this->filter)) {
   $resultEvents = $this->events->getEvents(array('filter' => $this->filter, 'start' => $start), 'normal', false, $orderBy);
  } else if ($this->isBlock) {
   $resultEvents = $this->events->getEvents(array('FID' => $_SESSION['fid']), 'complete', false, $orderBy);
   unset($_SESSION['fid']); # 2b improved soon
  } else {
   $resultEvents = $this->events->getEvents(array('start' => $start), 'complete', false, $orderBy);
  }

  if ($this->presentationMode == 'calendar') {

   $modulePath = drupal_get_path('module', 'aae_data');
   include_once $modulePath . '/kalender.php';

   $kal = new kalender();
   $resultKalender = $kal->show();

  }

  $resultTagCloud = db_query_range('SELECT COUNT(*) AS count, s.KID, s.kategorie FROM {aae_data_sparte} s INNER JOIN {aae_data_event_hat_sparte} hs ON s.KID = hs.hat_KID GROUP BY hs.hat_KID HAVING COUNT(*) > 0 ORDER BY count DESC', 0, 10);
  $itemsCount = db_query("SELECT COUNT(EID) AS count FROM " . $this->tbl_event)->fetchField();
  
  $festivals = db_select($this->tbl_festival, 'f')
    ->fields('f', array('name','alias'))
    ->execute()
    ->fetchAll();

  // Ausgabe der Events
  ob_start(); // Aktiviert "Render"-modus
  
  if ($this->isBlock) {
    
   $themePath = drupal_get_path('theme',$GLOBALS['theme']);
   drupal_add_js($themePath.'/js/CountUp.js');
   include_once $themePath . '/templates/neustadt_eventsblock.tpl.php';
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
