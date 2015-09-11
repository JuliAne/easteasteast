<header id="article-image" style="background:url(<?php print render (file_create_url($node->field_image['und'][0]['uri'])); ?>);">
 <div class="row">
  <h2 id="headline"><a href="<?= base_path().$node_url; ?>" title="<?= $title; ?>"><?= $title; ?></a></h2>
 </div>
</header>

<div id="content" class="row">
 <?php print render($content['body']); ?>
</div><!-- /#content -->


kopiert:

  <div class="info"><?php print $submitted ?><span class="terms"><?php print $terms ?></span></div>

<?php if ($links): ?>

    <?php if ($picture): ?>
      <br class='clear' />
    <?php endif; ?>
    <div class="links"><?php print $links ?></div>
<?php endif; ?>


<div id="article-<?php print $node->nid; ?>" class="article <?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php if ($title && !$page): ?>
    <div class="header article-header">
      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
        <h2<?php print $title_attributes; ?>>
          <a href="<?php print render()$node_url; ?>"><?php print $title; ?></a>
        </h2>
      <?php endif; ?>
      <?php print render($title_suffix); ?>
    </div>
  <?php endif; ?>

  <?php if ($display_submitted): ?>
    <div class="footer submitted">
      <?php print $user_picture; ?>
      <?php
        print t('Submitted by !username on !datetime', array(
          '!username' => $name,
          '!datetime' => '<span class="time pubdate" title="' . $datetime . '">' . $date . '</span>',
        ));
      ?>
    </div>
  <?php endif; ?>

  <div<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
  </div>

  <?php if ($links = render($content['links'])): ?>
    <div class="menu node-links clearfix"><?php print $links; ?></div>
  <?php endif; ?>

  <?php print render($content['comments']); ?>
