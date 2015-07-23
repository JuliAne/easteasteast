<?php if ($teaser): ?>
<h4><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h4>
<?= print $image; ?> Autor:<?= print $author; ?>

Print: <br />
<? print(print_r($content['comments'])); ?>
<br />

<?php   // Remove the "Add new comment" link on the teaser page or if the comment
  // form is being displayed on the same page.
  if ($teaser || !empty($content['comments']['comment_form'])) {
    unset($content['links']['comment']['#links']['comment-add']);
  }

  print render($content); ?>

<?php endif; ?>
