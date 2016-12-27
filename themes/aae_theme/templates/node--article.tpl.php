<?php $theme_path =  drupal_get_path('theme', $GLOBALS['theme']);
      global $base_url;
      global $user; ?>

<header id="article-image" style="background-image:url(<?php print render (file_create_url($node->field_image['und'][0]['uri'])); ?>);">

 <div class="aaeActionBar">
  <div class="row">
   <?php if (array_intersect(array('redakteur','administrator'), $user->roles)) : ?>
    <div class="large-3 large-offset-1 columns"><a href="<?= $node_url; ?>/edit" title="Artikel bearbeiten"><img src="<?= base_path().$theme_path; ?>/img/manage.svg" /><?= t('Bearbeiten'); ?></a></div>
    <?php endif; ?>

   <div class="large-6 columns right" style="text-align: right;">
    <a href="https://leipziger-ecken.de/rss.xml" title="<?= t('Beiträge als RSS'); ?>"><img src="<?= base_path().$theme_path; ?>/img/rss_small_icon.svg" /><?= t('Abonnieren'); ?></a>
    <a href="#comments" title="<?= $comment_count; ?> Kommentar(e)"><img src="<?= base_path().$theme_path; ?>/img/comments.svg" /><?= $comment_count; ?> Kommentar(e)</a>
    <a href="#share" class="popup-link" title="Journalbeitrag in den sozialen Netzwerken posten"><img src="<?= base_path().$theme_path; ?>/img/share.svg" /><?= t('Teilen'); ?></a>

    <div id="share" class="popup large-3 columns">

      <a target="_blank" href="https://twitter.com/intent/tweet?text=<?= $base_url.'/'.current_path(); ?>" title="Auf Twitter teilen" class="twitter button"><img alt="Twitter" src="<?= base_path().$theme_path; ?>/img/social-twitter.svg"><span></span></a>

      <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?= $base_url.'/'.current_path(); ?>" title="Auf Facebook teilen" class="fb button"><img alt="Facebook" src="<?= base_path().$theme_path; ?>/img/social-facebook.svg"><span></span></a>

      <a target="_blank" href="https://plus.google.com/share?url=<?= $base_url.'/'.current_path(); ?>" title="Auf Google+ teilen" class="g_plus button"><img alt="Google+" src="<?= base_path().$theme_path; ?>/img/social-googleplus-outline.svg"><span></span></a>

      <a target="_blank" href="https://sharetodiaspora.github.io/?title=<?= $title; ?> auf leipziger-ecken.de&url=<?= $base_url.'/'.current_path(); ?>" class="diaspora button" title="Teile auf Diaspora / Friendica"><img alt="Federated networks" src="<?= base_path().$theme_path; ?>/img/social-diaspora.svg"></a>

    </div>
   </div>

  </div><!-- /.row -->
 </div>

 <div id="headline" class="row">

  <div class="large-7 columns">
    <a href="<?= $node_url; ?>" title="<?= $title; ?>">
    <?php

      // Formatiere die Headline auf ein humanes Niveau
      $exploded = explode(" ", $title);
      $counter = 0;
      $res = array();
      foreach ($exploded as $n => $headline) {
       if (!empty($res) && strlen($res[$counter].$headline) < 25) {
        $res[$counter] .= ' '.$headline;
       } else {
        $counter++;
        $res[$counter] = $headline;
       }
      }

    foreach ($res as $r) { echo '<h2>'.$r.'</h2>';  } ?></a>
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
