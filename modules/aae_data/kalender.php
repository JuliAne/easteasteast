<?php
/**
 * @file kalender.php
 *
 * Stellt einen monatlichen Events-kalender dar
 * Copyright: The Internetz
 * TODO: Umstellen auf namespaces / Klassenmodell
 * TODO: Etwaige Doppelungen raus (Speicherintensive Date-Ermittlung)
 * TODO: Escape $_GET-Array
 * TODO: Put HTML into template
 */

namespace Drupal\AaeData;
#use Drupal\AaeData\events;

class kalender extends aae_data_helper {

 private $dayLabels = array("Mo","Di","Mi","Do","Fr","Sa","So");
 private $currentYear = 0;
 private $currentMonth = 0;
 private $currentDay = 0;
 private $currentDate = null;
 private $daysInMonth = 0;
 private $naviHref = null;
 private $year  = null;
 private $month = null;
 private $eventsForMonth = array();
 private $alreadyFilteredEIDs = array();

 public function __construct($filteredEIDs = NULL){
   
  parent::__construct();

  include_once('models/events.php');
  $this->events = new events();

  if (empty($filteredEIDs) && isset($_GET['EID'])){
   foreach ($_GET['EID'] as $eid){
    $filteredEIDs[$eid]['EID'] = $this->clearContent($eid);
    $filteredEIDs[$eid] = (object)$filteredEIDs[$eid];
   }
  }
 
  if ($filteredEIDs){
   if ($filteredEIDs == 'empty'){
    $this->alreadyFilteredEIDs[] = 999999; // = 0 results
   } else {
    foreach ($filteredEIDs as $EID){
     $this->alreadyFilteredEIDs[] = $EID->EID;
    }
   }
  }

 }

 public function run(){
  return $this->show();
 }

 public function show() {

   if (null == $this->year && isset($_GET['year'])) {
     $this->year = $this->clearContent($_GET['year']);
   } else if (null == $this->year) {
     $this->year = date('Y');
   }
   if (null == $this->month && isset($_GET['month'])) {
     $this->month = $this->clearContent($_GET['month']);
   } else if (null == $this->month) {
     $this->month = date('m');
   }

   $this->currentYear = $this->year;
   $this->currentMonth = $this->month;
   $this->daysInMonth = $this->_daysInMonth($this->month, $this->year);
   $content = '<div id="calendar">' .
     '<div class="box">' .
       $this->_createNavi().
     '</div>'.
     '<div class="box-content">' .
       '<ul class="label">' . $this->_createLabels() . '</ul>';
       $content .= '<div class="clear"></div>';
       $content .= '<ul class="dates">';

       $eventsQuery = array(
        'start' => array(
          '0' => array(
           'date' => (new \DateTime($this->year.'-'.$this->month.'-01'))->format('Y-m-d 00:00:00'),
           'operator' => '>='
          ),
          '1' => array(
           'date' => (new \DateTime(date('Y-m-t', mktime(0, 0, 0, $this->month, 1, $this->year))))->format('Y-m-d 23:59:59'),
           'operator' => '<='
          )
        )
       );

       if (!empty($this->alreadyFilteredEIDs)) {
        $eventsQuery['EID'] = $this->alreadyFilteredEIDs;  
       }

       $eventsResults = $this->events->getEvents($eventsQuery, 'minimal');

      // Sort'em
      foreach ($eventsResults as $eventData) {
       $day = (!substr($eventData->start->format('d'),0,1)) ? substr($eventData->start->format('d'),1,2) : $eventData->start->format('d');
       $this->eventsForMonth[$day][] = $eventData;
      }

      $weeksInMonth = $this->_weeksInMonth($this->month, $this->year);

      for ($i=0; $i < $weeksInMonth; $i++) {
       for ($j=1;$j<=7;$j++) {
         $content .= $this->_showDay($i * 7 + $j);
       }
      }

       $content .= '</ul>';
       $content .= '<div class="clear"></div>';
     $content .= '</div>';
   $content .= '</div>';
   return $content;
 }

 /**
  * create the li element for ul
  */
 private function _showDay($cellNumber) {

   if ($this->currentDay == 0) {
     $firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
     if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
       $this->currentDay = 1;
     }
   }
   if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
     $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . $this->currentDay));
     $cellContent = $this->currentDay;
     //$events = null;

    # echo $this->currentDay;
    if (!empty($this->eventsForMonth[$this->currentDay])){
     foreach ($this->eventsForMonth[$this->currentDay] as $row) {
      $events .= $row->name.'&#10';
     }
    } 

     $countrows = count($this->eventsForMonth[$this->currentDay]);
     $this->currentDay++;

   } else {

     $this->currentDate = null;
     $cellContent = null;

   }
   if ($countrows == 0) {
     return '<li id="li-' . $this->currentDate . '" class="' . ($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')) . ($cellContent==null?'mask':'') . '">' . $cellContent . '</li>';
   } else {
     return '<li id="event" data-nr="'.$countrows.'" title="'.$events.'" class="' . ($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')) . ($cellContent==null?'mask':'') . '"><a href="'.base_path().'events/?day=' . $this->currentDate . '" rel="nofollow">' . $cellContent . '</a></li>';
   }
 }

 /**
  * create navigation
  */
 private function _createNavi() {

   $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
   $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
   $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
   $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
   $filterParam = (!empty($this->alreadyFilteredEIDs) ? '&EID[]='.implode('&EID[]=',$this->alreadyFilteredEIDs) : NULL);

   return
     '<div class="header">'.
       '<a class="prev" href="'.base_path().'ajax/getKalender/?month=' . sprintf('%02d',$preMonth) . '&year=' . $preYear . $filterParam .'" rel="nofollow"><<</a>'.
         '<span class="title">' . date('Y M',strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>'.
         // $this->monat_lang[01];
       '<a class="next" href="'.base_path().'ajax/getKalender/?month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . $filterParam .'" rel="nofollow">>></a>'.
     '</div>';
 }

 /**
  * create calendar week labels
  */
 private function _createLabels() {
   $content = '';
   foreach ($this->dayLabels as $index=>$label) {
     $content .= '<li class="start title '.$label.'">' . $label . '</li>';
   }
   return $content;
 }

 /**
  * calculate number of weeks in a particular month
  */
 private function _weeksInMonth($month=null, $year=null) {

   // find number of days in this month
   $daysInMonths = $this->_daysInMonth($month,$year);
   $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
   $monthEndingDay = date('N',strtotime($year . '-' . $month . '-' . $daysInMonths));
   $monthStartDay = date('N',strtotime($year . '-' . $month . '-01'));

   if ($monthEndingDay < $monthStartDay) {
    $numOfweeks++;
   }
   return $numOfweeks;
 }

 /**
  * calculate number of days in a particular month
  */
 private function _daysInMonth($month=null, $year=null) {
   return date('t',strtotime($year . '-' . $month . '-01'));
 }

} // end class kalender
