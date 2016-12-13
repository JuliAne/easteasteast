<h3><?= t('Kontaktinformationen'); ?> <i><?= $resultAkteur[0]->name; ?></i></h3><br />
<?php if ($resultAkteur[0]->ansprechpartner != '') : ?>
<p class="grey"><?= t('Ansprechpartner'); ?>: <?= $resultAkteur[0]->ansprechpartner; ?> (<?= $resultAkteur[0]->funktion; ?>)</p>
<?php endif; ?>
<div class="divider"></div>
<?php if ($resultAkteur[0]->telefon != '') : ?>
<p><strong><?= t('Telefon'); ?>:</strong> <?= $resultAkteur[0]->telefon; ?></p>
<?php endif; ?>
<p><strong>Mail:</strong> <a href="mailto:<?= $resultAkteur[0]->email; ?>"><?= $resultAkteur[0]->email; ?></a></p>
<div class="divider"></div>
