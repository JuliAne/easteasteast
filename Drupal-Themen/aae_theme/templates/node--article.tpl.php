<header id="article-image" style="background:url(<?php print render (file_create_url($node->field_image['und'][0]['uri'])); ?>);">
 <div class="row">
  <h2 id="headline"><a href="<?= base_path().$node_url; ?>" title="<?= $title; ?>"><?= $title; ?></a></h2>
 </div>
</header>

<div id="content" class="row">
 <?php print render($content['body']); ?>
</div><!-- /#content -->

  <div class="info"><?php print $submitted ?><span class="terms"><?php print $terms ?></span></div>
  
<?php if ($links): ?>

    <?php if ($picture): ?>
      <br class='clear' />
    <?php endif; ?>
    <div class="links"><?php print $links ?></div>
<?php endif; ?>

