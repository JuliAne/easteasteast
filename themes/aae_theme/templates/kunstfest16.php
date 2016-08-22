<?php

// Hardcoded FB Open Graph Data stuff
$og_title = array(
  '#tag' => 'meta',
  '#attributes' => array(
    'property' => 'og:title',
    'content' => 'Kunstfest Neustadt 2016 | 02. - 10. Juli | Bildungsferne so nah?',
  ),
);
$og_image = array(
  '#tag' => 'meta',
  '#attributes' => array(
    'property' => 'og:image',
    'content' => 'https://leipziger-ecken.de/sites/all/themes/aae_theme/neustadt/kunstfest_bg.jpg',
  ),
);
$og_desc = array(
 '#tag' => 'meta',
 '#attributes' => array(
   'property' => 'og:description',
   'content' => 'Bildungsferne - so nah? Offenes Kunsfest am Neustädter Markt vom 02. - 10. Juli 2016',
 ),
);
 
drupal_add_html_head($og_title, 'og_title');
drupal_add_html_head($og_image, 'og_image');
drupal_add_html_head($og_desc, 'og_description');

?>

<style type="text/css">
#footer {background-image:unset;}
</style>

<div id="neustadtPageBg"></div>

<header id="neustadtPageHeader" class="nodisplay pageHeader">
  <h2>Kunstfest Neustadt <a href="<?= base_path(); ?>events/rss" title="<?= t('Alle Events als RSS-Feed'); ?>"><img id="svg_logo" src="<?= base_path().path_to_theme(); ?>/img/rss.svg" /></a></h2>
  <p>Bildungsferne - so nah? 02. - 10. Juli 2016.</p>
</header>

<aside id="neustadtIntro" class="row">
 <div id="festivalEvents"><img src="<?= base_path().path_to_theme(); ?>/img/festival_event.png" /><strong id="festivalEventsCount">0</strong> Veranstaltungen</div>
 <div id="festivalAkteur"><img src="<?= base_path().path_to_theme(); ?>/img/festival_akteur.png" /><strong id="festivalAkteureCount">0</strong> Akteure</div>
 <div id="festivalLocation"><img src="<?= base_path().path_to_theme(); ?>/img/festival_location.png" /><strong>Neustadt</strong> & <strong>Volkmarsdorf</strong></div>
</aside>

<div id="events" class="neustadtEvents row">
  <div id="events_content" class="large-12 small-12 columns">

   <ul class="tabs large-12 columns" id="events-tabs">
    <?php #$path = explode("/", current_path());
          // Need a universal menu-method here!
          $path = request_uri(); ?>
   
    <li class="tabs-title<?= ($path == '/kunstfest16' ? ' is-active' : ''); ?>"><a href="https://leipziger-ecken.de/kunstfest16"<?= ($path == '/kunstfest16' ? ' aria-selected="true"' : ''); ?>>Startseite</a></li>
    <li class="tabs-title"><a href="https://leipziger-ecken.de/akteurprofil/86">Das Festival</a></li>
    <li class="tabs-title<?= ($path == '/kunstfest16?presentation=calendar' ? ' is-active' : ''); ?>"><a href="https://leipziger-ecken.de/kunstfest16?presentation=calendar"<?= ($path == '/kunstfest16?presentation=calendar' ? ' aria-selected="true"' : ''); ?>>Kalender</a></li>
    <li class="tabs-title<?= ($path == '/kunstfest16/impressionen' ? ' is-active' : ''); ?>"><a href="https://leipziger-ecken.de/kunstfest16/impressionen"<?= ($path == '/kunstfest16/impressionen' ? ' aria-selected="true"' : ''); ?>>Impressionen</a></li>
    <?php global $user; if ($user->uid == 238) : ?>
    <li class="right tabs-title"><a href="https://leipziger-ecken.de/admin/page-builder">+ Seite hinzufügen</a></li>
    <?php endif; ?>
    
    <ul id="presentationFilter" class="button-group round large-3 columns right">
     <li class="right"><a target="_blank" href="mailto:kontakt@poege-haus.de" title="Das Pöge-Haus kontaktieren" class="twitter button"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/paperplane.svg"><span></span></a></li>
     <li class="right">
    <a target="_blank" href="https://www.facebook.com/Kunstfest-Neustadt-1436373796669812/" title="Besuch uns auf Facebook!" class="fb button"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a></li>
    </ul>
   </ul>
   
   <!-- PAGE BUILDER CONTENT -->
   
   <?php
$data = !empty($page->data) ? unserialize($page->data) : array();

$rows = isset($data['rows']) ? $data['rows'] : array();
if (!empty($rows)) {
  uasort($rows, 'drupal_sort_weight'); // sorted rows
}
$columns_arr = $data['columns'];
$elements_arr = $data['elements'];
?>
<?php if (!empty($rows)): ?>
  <div id="page-builder-page-<?php print $page->id; ?>" class="page-builder-wrapper">
    <?php foreach ($rows as $row_id => $row): ?>
      <?php
      $row_attributes = '';
      $row_class_id = !empty($row['row_settings']['css_id']) ? ' id="' . $row['row_settings']['css_id'] . '"' : '';
      if (empty($row_class_id)) {
        $row_class_id = ' id="page-builder-section-' . $row_id . '"';
      }
      $row_classes_arr = array(
          'page-builder-row-section', // do not remove this default css class name
          'page-builder-row-section-' . $row_id, // do not remove this default css class name
      );
      if (isset($row['row_settings']['use_video']) && $row['row_settings']['use_video']) {
        $row_classes_arr[] = 'page-builder-video-section';
        $v = $row['row_settings']['video'];
        $v['containment'] = '.page-builder-video-section .innerVideo';
        $video_data = json_encode($v);
        $row_attributes = " data-property='$video_data'";
      }
      if(!empty($row['row_settings']['use_parallax'])){
	       $row_classes_arr[] = 'page-builder-row-parallax';
	  }
      if(!empty($row['row_settings']['parallax']['use_overlay'])){
        $row_classes_arr[] = 'page-builder-row-overlay';
      }
      $row_css_classes = implode(' ', $row_classes_arr);
      if (!empty($row['row_settings']['css_class'])) {
        $row_css_classes .= ' ' . $row['row_settings']['css_class'];
      }

      $inner_class = 'page-builder-row-inner';
      $inner_class .=!empty($row['row_settings']['inner_css_class']) ? ' ' . $row['row_settings']['inner_css_class'] : '';
      ?>
      <section<?php print $row_class_id; ?> class="<?php print $row_css_classes; ?>"<?php print $row_attributes; ?>>
        <?php if(!empty($row['row_settings']['parallax']['use_overlay'])):?>
        <div class="page-builder-bg-overlay"></div>
        <?php endif;?>
        <?php if (isset($row['row_settings']['use_video']) && $row['row_settings']['use_video']): ?>
          <?php
          $video_url = !empty($row['row_settings']['video']['videoURL']) ? $row['row_settings']['video']['videoURL'] : '';
          ?>
          <!-- fallback video -->
          <div class="fallbackVideo" style="display:none;">
            <iframe width="560" height="315" src="<?php print $video_url; ?>" frameborder="0" allowfullscreen></iframe>
          </div>

          <div class="innerVideo"></div>
        <?php endif; ?>

        <div class="<?php print $inner_class; ?>">
          <?php if (!empty($row['row_settings']['section_title'])): ?>
            <div class="page-builder-row-title">
              <h2><?php print $row['row_settings']['section_title']; ?></h2>
              <hr />
            </div>
          <?php endif; ?>
          <?php if (!empty($columns_arr[$row_id])): ?>
            <div class="page-builder-column-wrapper">
              <?php
              $columns = $columns_arr[$row_id];
              uasort($columns, 'drupal_sort_weight');
              ?>
              <?php foreach ($columns as $col_id => $col_val): ?>
                <?php $grid_size = $col_val['settings']['grid_size']; ?>
                <?php $output = _page_builder_get_data($row_id, $col_id, $elements_arr); ?>
                <?php if (!empty($output)): ?>
                  <?php
                  $colum_class = 'page-builder-column col-md-' . $grid_size;
                  if ($row['row_settings']['use_slider']) {
                    $colum_class = 'page-builder-slide-item page-builder-slider-item-' . $col_id;
                  }
                  ?>
                  <div class="<?php print $colum_class; ?>">
                    <?php if (!empty($elements_arr[$row_id][$col_id])): ?>
                      <?php
                      $elements = $elements_arr[$row_id][$col_id];
                      uasort($elements, 'drupal_sort_weight');
                      ?>
                      <?php foreach ($elements as $e_id => $e_val): ?>
                        <div class="page-builder-element">
                          <?php if (isset($output[$e_id])): ?>
                            <?php if ($e_val['show_title'] && !empty($output)): ?>
                              <h4 class="page-builder-element-title"><?php print $e_val['title']; ?></h4>
                            <?php endif; ?>
                            <?php print $output[$e_id]; ?>
                          <?php endif; ?>
                        </div>


                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
   <!-- END -->
   
  <?php #require_once('neustadt_eventsblock.tpl.php'); ?>

</div>
 </div>
 
 <div id="festivalFooter" class="row">
  <h4>Sponsoren & Kooperationen</h4>
  <div class="large-3 large-offset-1 columns"><img src="<?= base_path().path_to_theme(); ?>/neustadt/sponsor_leipzig.png" /></div>
  <div class="large-3 large-offset-1 columns" style="padding-top:7px;"><img src="<?= base_path().path_to_theme(); ?>/neustadt/sponsor_hww.png" /></div>
  <div class="large-3 large-offset-1 columns" style="width:15%"><img src="<?= base_path().path_to_theme(); ?>/neustadt/sponsor_nmb.png" /></div>

 </div>
