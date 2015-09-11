<header id="article-image" style="background:url(<?php print render (file_create_url($node->field_image['und'][0]['uri'])); ?>);">
 <div class="row">

  <div class="large-8 columns">
   <h2 id="headline"><a href="<?= base_path().$node_url; ?>" title="<?= $title; ?>"><?= $title; ?></a></h2>
  </div>

  <div class="large-3 large-offset-1 columns">
   <?php print $user_picture; ?>
   <p>2<?= $comment_count; ?> Kommentare</p>
   <p>Von <strong><?= $name; ?></strong> am <?= $date; ?></p>
  </div>

 </div>
</header>

<div id="content" class="row">
 <?php print render($content['body']); ?>
 <div class="divider"></div>
 <div class="links"><?php print $links ?></div>
 <div class="info"><?php print $submitted ?><span class="terms"><?php print $terms ?></span></div>
 <?php print $content_attributes; ?>
</div><!-- /#content -->

<div id="comments" class="row">
 <?php print render($content['comments']); ?>
</div>
