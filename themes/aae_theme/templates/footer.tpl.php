<section class="section" id="footer">

 <div class="divider"></div>

 <div class="row">

 <div class="large-4 columns" id="about">
  <h4>&Uuml;ber den Stadtteil.</h4>
  <?php print render($page['footer']); ?>
 </div>

 <div class="large-4 columns" id="mini-nav">
  <h4>Navigation.</h4>
  <?php print render($page['mainnav']); ?>
 </div>

 <div id="mini-calendar" class="large-4 columns">
  <h4>Events.</h4>
  <!--<div class="month-view"></div>-->

   <div id="aae_calendar" style="margin-top:-15px;">
   <?php $modulePath = drupal_get_path('module', 'aae_data');
         include_once $modulePath . '/kalender.php';

         $kal = new Drupal\AaeData\kalender();
         echo $kal->show(); ?>
   </div>
 </div>

 </div>
</section><!-- /#footer -->
