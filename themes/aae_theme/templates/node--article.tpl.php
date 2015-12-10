<?php $theme_path =  drupal_get_path('theme', $GLOBALS['theme']);
      global $base_url; ?>

<header id="article-image" style="background-image:url(<?php print render (file_create_url($node->field_image['und'][0]['uri'])); ?>);">

 <div class="aaeActionBar">
  <div class="row">
   <?php //if ($hat_recht): ?>
    <div class="large-3 large-offset-1 columns"><a href="<?= $node_url; ?>/edit" title="Artikel bearbeiten"><img src="<?= base_path().$theme_path; ?>/img/manage.svg" />Bearbeiten</a></div>
    <?php // endif; ?>

   <div class="large-4 columns right" style="text-align: right;">
    <a href="#comments" title="<?= $comment_count; ?> Kommentar(e)"><img src="<?= base_path().$theme_path; ?>/img/comments.svg" /><?= $comment_count; ?> Kommentar(e)</a>
    <a href="#share" class="popup-link" title="Journalbeitrag in den sozialen Netzwerken posten"><img src="<?= base_path().$theme_path; ?>/img/share.svg" />Teilen</a>

    <div id="share" class="popup large-3 columns">

      <a target="_blank" href="https://twitter.com/intent/tweet?text=<?= $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="icons/twitter.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/twitter.png"><span></span></a>

      <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="icons/fb.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/fb.png"><span></span></a>

      <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="icons/g_plus.png" src="http://fadeco.info/system/cms/themes/defaults/img/icons/g_plus.png"><span></span></a>

      <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $title; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="" src="https://sharetodiaspora.github.io/favicon.png"></a>

    </div>
   </div>

  </div><!-- /.row -->
 </div>

 <div id="headline" class="row">

  <div class="large-8 columns">

   <h2><a href="<?= $node_url; ?>" title="<?= $title; ?>"><?= $title; ?></a></h2>
   <h3>Von <strong><?= $name; ?></strong></h3>

  </div>

  <div class="large-3 large-offset-1 columns">
   <?php print $user_picture; ?>
  </div>

 </div>
</header>

<div id="content" class="row">
 <?php print render($content['body']); ?>
 <div class="divider"></div>
</div>

<aside id="meta_info">
 <div class="row">
  <div class="large-5 columns info"><p>Veröffentlicht am <?= $date; ?></p></div>
  <div class="large-4 right" style="text-align:right;"><p>Zurück zum <a href="<?= base_path(); ?>journal">Journal</a>.</p></div>
 </div>
</aside>

<div id="comments">
  <div class="divider"></div>
  <div class="row">
   <?php print render($content['comments']); ?>
  </div>
</div>
