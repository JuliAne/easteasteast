<?php if ($teaser): ?>
<h4><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h4>
<?= $image; ?> Autor:<?= $author; ?>
<?php print render($content['comments']); ?>

<?php   // Remove the "Add new comment" link on the teaser page or if the comment
  // form is being displayed on the same page.
  if ($teaser || !empty($content['comments']['comment_form'])) {
    unset($content['links']['comment']['#links']['comment-add']);
  }

  print render($content); ?>

<?php endif; ?>
