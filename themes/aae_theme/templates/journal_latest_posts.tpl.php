<?php
  foreach ($nodes as $node) :

   $url = url('node/' . $node->nid, array('absolute' => TRUE)); ?>

   <div class="row article" style="padding: 8px 0;">
    <div class="large-2 small-3 columns" style="background-image:url('<?php if (!empty($node->field_image['und'][0]['filename'])) : ?><?= base_path().'sites/default/files/styles/large/public/field/image/'.$node->field_image['und'][0]['filename']; ?><?php else: ?><?= base_path().'sites/all/themes/aae_theme/img/journal_bg.png'; ?><?php endif; ?>');"></div>

    <div class="large-8 large-offset-2 small-7 columns" style="text-align:right;">
     <h3><a href="<?= $url; ?>"><?= $node->title; ?></a></h3>
     <p style="padding: 10px 0;line-height:1.5em;"><?= substr($node->body['und'][0]['value'],0,260); ?>...</p>
     <p>Von <strong><?= $node->name; ?></strong> - <a href="<?= $url; ?>#comments"><?= $node->comment_count; ?> <?= t('Kommentare'); ?></a></p>
    </div>
   </div><!-- /.article -->

   <?php endforeach; ?>
