<section class="section" id="footer">

 <div class="divider"></div>

 <div id="footerContent" class="row">

 <div class="large-4 columns" id="about">
  <h4><?= t('Über den Stadtteil.'); ?></h4>
  <?php print render($page['footer']); ?>
 </div>

 <div class="large-4 columns" id="mini-nav">
  <h4>Schaltzentrale.</h4>
  <?php // To replace with a native menu, call "print render($page['mainnav']);" ?>
  <div class="region region-mainnav">
    <div id="block-system-main-menu" class="block block-system contextual-links-region block-menu">
     <div class="content">
      <ul class="menu">
       <li><a href="<?= base_path(); ?>faq" title="FAQ">FAQ</a></li>
       <li><a href="<?= base_path(); ?>impressum" title="Impressum">Impressum</a></li>
       <li><a href="<?= base_path(); ?>contact" title="Kontaktiere uns">Kontakt</a></li>
       <li id="social-networks"><a href="https://diasp.org/people/1c022a8001820134ae16782bcb452bd5" title="<?= t('Folge uns auf Diaspora'); ?>">@Diaspora</a><a href="https://www.facebook.com/LeipzigerEcken/" title="<?= t('Folge uns auf Facebook'); ?>">@Facebook</a></li>
      </ul>
     </div>
    </div>
  </div>
 </div>

 <div id="mini-calendar" class="large-4 columns">
   <div id="aaeCalendar" style="margin-top:-15px;">
   <?php $modulePath = drupal_get_path('module', 'aae_data');
         include_once $modulePath . '/kalender.php';

         $kal = new Drupal\AaeData\kalender();
         echo $kal->show(); ?>
   </div>
 </div>

 </div><!-- /#footerContent -->
 
 <div class="row hide-on-small-only" style="display:none;">
  <div class="large-3 columns">
   <p>Teils unter Förderung von</p>
  </div>
  <div class="large-9 columns">
  <img src="/sites/all/themes/aae_theme/img/sponsors.png" />
  </div>
 </div>

</section><!-- /#footer -->
