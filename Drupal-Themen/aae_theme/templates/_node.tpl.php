<?php if ($teaser): ?>

<h4><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h4>
<?= print $image; ?> Autor:<?= print $author; ?>

Print: <br />
<? print($content); ?>
<br />


<?php endif; ?>
