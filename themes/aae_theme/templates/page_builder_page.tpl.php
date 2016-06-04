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
