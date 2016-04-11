<!--
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
   </div>

   <?php endforeach; ?>
 -->
 <div class="row">
 <?php
   foreach ($nodes as $node) :

    $url = url('node/' . $node->nid, array('absolute' => TRUE)); ?>

  <div class="large-3 jEntry columns">

     <div class="jEntryPrev large-12 columns">

     <div class="jEntryImg large-2 columns" style="background-image:url('<?php if (!empty($node->field_image['und'][0]['filename'])) : ?><?= base_path().'sites/default/files/styles/large/public/field/image/'.$node->field_image['und'][0]['filename']; ?><?php else: ?><?= base_path().'sites/all/themes/aae_theme/img/journal_bg.png'; ?><?php endif; ?>');"></div>

     <div class="jEntryContent large-8 right columns">
      <h3><a href="<?= $url; ?>"><?= $node->title; ?></a></h3>
      <?= substr($node->body['und'][0]['value'],0,205); ?>
      <p><a href="<?= $url; ?>"><?= t('Weiterlesen'); ?>...</a></p>
     </div>
    </div><!-- /.article -->

    <div class="jFooter large-12 columns">
     <div class="jAside">
      <span><a href="<?= $url; ?>#comments"><?= $node->comment_count; ?> <?= ($node->comment_count != 0 ? t('Kommentare') : t('Kommentar')); ?></a></span><br /><span>Von <strong><?= $node->name; ?></strong></span>
     </div>
     <div id="share">
       <a target="_blank" href="https://twitter.com/intent/tweet?text=<?php $url; ?>" title="<?= t('Auf !network teilen',array('!network'=>'Twitter')); ?>" class="twitter"><img alt="Twitter" src="<?= base_path().path_to_theme(); ?>/img/social-twitter.svg"><span></span></a>
       <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $url; ?>" title="<?= t('Auf !network teilen',array('!network'=>'Facebook')); ?>" class="fb"><img alt="Facebook" src="<?= base_path().path_to_theme(); ?>/img/social-facebook.svg"><span></span></a>
     </div>
    </div>

</div>

    <?php endforeach; ?>
  </div>
