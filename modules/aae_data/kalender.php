<?php
/**
 * Stellt einen Kalender fuer die Events dar
 */

class kalender extends aae_data_helper {

 private $dayLabels = array("Mo","Di","Mi","Do","Fr","Sa","So");
 private $currentYear=0;
 private $currentMonth=0;
 private $currentDay=0;
 private $currentDate=null;
 private $daysInMonth=0;
 private $naviHref= null;

 public function __construct(){

 }

 public function run(){

  return $this->show();

 }

 public function show() {
  $year  == null;
  $month == null;

   if (null == $year && isset($_GET['year'])) {
     $year = $_GET['year'];
   } else if (null == $year) {
   $year = date("Y", time());
   }
   if (null == $month && isset($_GET['month'])) {
   $month = $_GET['month'];
   } else if (null == $month) {
     $month = date("m", time());
   }

   $this->currentYear = $year;
   $this->currentMonth = $month;
   $this->daysInMonth=$this->_daysInMonth($month, $year);
   $content = '<div id="calendar">' .
     '<div class="box">' .
       $this->_createNavi().
     '</div>'.
     '<div class="box-content">' .
       '<ul class="secondary label">' . $this->_createLabels() . '</ul>';
       $content .= '<div class="clear"></div>';
       $content .= '<ul class="dates">';

         $weeksInMonth = $this->_weeksInMonth($month, $year);
         // Create weeks in a month
         for ($i=0; $i<$weeksInMonth; $i++) {
         //Create days in a week
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

     // DB-Abfrage aller Events, die an diesem Tag stattfinden
     $resultEvents = db_select($this->tbl_event, 'e')
       ->fields('e', array(
       'start_ts',
       'name',
       'EID',
       ))
       ->condition('start_ts', $this->currentDate . '%', 'LIKE')
       // WHERE -> FILTERS!
       ->orderBy('name', 'ASC')
       ->execute();

     foreach ($resultEvents as $row) {
      $events .= $row->name.' ';
     }

     $countrows = $resultEvents->rowCount();
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

   return
     '<div class="header">'.
       '<a class="prev" href="'.base_path().'ajax/getKalender/?month=' . sprintf('%02d',$preMonth) . '&year=' . $preYear . '" rel="nofollow"><<</a>'.
         '<span class="title">' . date('Y M',strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . '</span>'.
       '<a class="next" href="'.base_path().'ajax/getKalender/?month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . '" rel="nofollow">>></a>'.
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
   if (null == ($year)) {
     $year =  date("Y",time());
   }
   if (null == ($month)) {
     $month = date("m",time());
   }

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
   if (null == ($year)) {
     $year =  date("Y",time());
   }
   if (null == ($month)) {
     $month = date("m",time());
   }
   return date('t',strtotime($year . '-' . $month . '-01'));
 }

} // end class kalender
