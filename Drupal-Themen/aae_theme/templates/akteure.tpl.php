<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Akteure</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="small button round right" href="<?= base_path(); ?>?q=Akteurformular">+ Akteur hinzufügen</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding: 15px 0;">

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 columns pcard">
   <header <?php if($akteur->bild != '') echo 'style="background:url('.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'?q=Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
     <p><?= $akteur->beschreibung; ?></p>
     <p><a href="<?= base_path(); ?>?q=Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
    </section>
   </div>
<?php endforeach; ?>

</div>

<div class="divider"></div>

<div class="row">
  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
    <li class="arrow"><a href="<?= base_path(); ?>?q=Akteure/1">&laquo;</a></li>

    <?php for ($i=1; $i<=$maxPages; $i++) {
     if ($i == $currentPageNr) echo '<li class="current"><a href="">'.$i.'</a></li>';
     else echo '<li><a href="'.base_path().'?q=Akteure/'.$i.'">'.$i.'</a></li>';
     //<!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
     } ?>

    <li class="arrow"><a href="<?= base_path(); ?>?q=Akteure/<?= $maxPages ?>">&raquo;</a></li>
 </ul>
</div>
