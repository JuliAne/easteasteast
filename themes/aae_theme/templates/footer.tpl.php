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
  <div class="month-view">
   <div class="date-nav-wrapper clear-block">
    <div class="date-nav">
     <div class="date-heading">
     <a href="<?= base_path(); ?>Kalender" title="">Großer Kalender</a>
     </div>
    </div>
   </div>

   <?php $modulePath = drupal_get_path('module', 'aae_data');
         include_once $modulePath . '/kalender.php';

         $kal = new kalender();
         echo $kal->show(); ?>
</div>
 </div>

 </div>
</section><!-- /#footer -->
