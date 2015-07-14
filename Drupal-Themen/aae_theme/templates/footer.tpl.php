 <div class="row">

 <div class="large-4 columns" id="about">
  <h4>&Uuml;ber den Stadtteil.</h4>
  <?php print render($page['footer']); ?>
 </div>

 <div class="large-4 columns" id="mini-nav">
  <h4>Navigation.</h4>

  <?php if ($main_menu): ?>
     <?php print theme('links__system_main_menu', array(
           'links' => $main_menu,
           'attributes' => array(
             'class' => array('links', 'clearfix', 'side-nav'),
           )
         )); ?>
  <?php endif; ?>

 </div>

 <div id="mini-calendar" class="large-4 columns">
  <h4>Kalender.</h4>
  <div class="month-view">
   <div class="date-nav-wrapper clear-block">
    <div class="date-nav">
     <div class="date-heading">
     <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/month/2015-06" title="View full page month">Juni 2015</a>
     </div>
    </div>
   </div>

   <table class="mini">
    <thead>
     <tr>
             <th class="days mon">
         M        </th>
             <th class="days tue">
         T        </th>
             <th class="days wed">
         W        </th>
             <th class="days thu">
         T        </th>
             <th class="days fri">
         F        </th>
             <th class="days sat">
         S        </th>
             <th class="days sun">
         S        </th>
         </tr>
     </thead>
     <tbody>
         <tr>
                 <td id="calendar-2015-06-01" class="mon mini today has-no-events">
           <div class="year mini-day-off"> 1 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-02" class="tue mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-02">2</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-03" class="wed mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-03">3</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-04" class="thu mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-04">4</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-05" class="fri mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-05">5</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-06" class="sat mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-06">6</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-07" class="sun mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-07">7</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
             </tr>
         <tr>
                 <td id="calendar-2015-06-08" class="mon mini future has-no-events">
           <div class="year mini-day-off"> 8 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-09" class="tue mini future has-events">
           <div class="year mini-day-on"> <a href="http://pcai042.informatik.uni-leipzig.de/~swp15-aae/drupal/?q=calendar-node-field-datum/day/2015-06-09">9</a> </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-10" class="wed mini future has-no-events">
           <div class="year mini-day-off"> 10 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-11" class="thu mini future has-no-events">
           <div class="year mini-day-off"> 11 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-12" class="fri mini future has-no-events">
           <div class="year mini-day-off"> 12 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-13" class="sat mini future has-no-events">
           <div class="year mini-day-off"> 13 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-14" class="sun mini future has-no-events">
           <div class="year mini-day-off"> 14 </div><div class="calendar-empty">&nbsp;</div>
         </td>
             </tr>
         <tr>
                 <td id="calendar-2015-06-15" class="mon mini future has-no-events">
           <div class="year mini-day-off"> 15 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-16" class="tue mini future has-no-events">
           <div class="year mini-day-off"> 16 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-17" class="wed mini future has-no-events">
           <div class="year mini-day-off"> 17 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-18" class="thu mini future has-no-events">
           <div class="year mini-day-off"> 18 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-19" class="fri mini future has-no-events">
           <div class="year mini-day-off"> 19 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-20" class="sat mini future has-no-events">
           <div class="year mini-day-off"> 20 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-21" class="sun mini future has-no-events">
           <div class="year mini-day-off"> 21 </div><div class="calendar-empty">&nbsp;</div>
         </td>
             </tr>
         <tr>
                 <td id="calendar-2015-06-22" class="mon mini future has-no-events">
           <div class="year mini-day-off"> 22 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-23" class="tue mini future has-no-events">
           <div class="year mini-day-off"> 23 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-24" class="wed mini future has-no-events">
           <div class="year mini-day-off"> 24 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-25" class="thu mini future has-no-events">
           <div class="year mini-day-off"> 25 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-26" class="fri mini future has-no-events">
           <div class="year mini-day-off"> 26 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-27" class="sat mini future has-no-events">
           <div class="year mini-day-off"> 27 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-28" class="sun mini future has-no-events">
           <div class="year mini-day-off"> 28 </div><div class="calendar-empty">&nbsp;</div>
         </td>
             </tr>
         <tr>
                 <td id="calendar-2015-06-29" class="mon mini future has-no-events">
           <div class="year mini-day-off"> 29 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-06-30" class="tue mini future has-no-events">
           <div class="year mini-day-off"> 30 </div><div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-07-01" class="wed mini empty">
           <div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-07-02" class="thu mini empty">
           <div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-07-03" class="fri mini empty">
           <div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-07-04" class="sat mini empty">
           <div class="calendar-empty">&nbsp;</div>
         </td>
                 <td id="calendar-2015-07-05" class="sun mini empty">
           <div class="calendar-empty">&nbsp;</div>
         </td>
             </tr>
     </tbody>
</table>
</div>
 </div>

</div>
