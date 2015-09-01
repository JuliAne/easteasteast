<div class="row">

<h3 class="large-4 columns"><strong><?= $itemsCount; ?></strong> Akteure</h3>

<?php if(user_is_logged_in()) : ?>
  <a class="small button round right" href="<?= base_path(); ?>Akteurformular">+ Akteur hinzufügen</a><br />
<?php else : ?>
  <a class="small secondary button round right" href="<?= base_path(); ?>user/register">+ Akteur hinzufügen</a><br />
<?php endif; ?>

</div>
<div class="divider"></div>

<div id="akteure" class="row" style="padding: 15px 0;">

<?php foreach($resultAkteure as $akteur): ?>
  <div class="large-3 large-offset-1 columns pcard" style="margin-top:10px;">
   <header <?php if($akteur->bild != '') echo 'style="background:url('.$akteur->bild.');"'; ?>>
     <h3><a href="<?= base_path().'Akteurprofil/'.$akteur->AID; ?>"><?= $akteur->name; ?></a></h3>
    </header>
    <section>
     <?php if ($akteur->beschreibung !== ''): ?>
     <p><?= $kurzbeschreibung = substr ($akteur->beschreibung, 0, 120)."..."; ?></p>
     <?php endif; ?>
     <p><a href="<?= base_path(); ?>Akteurprofil/<?= $akteur->AID; ?>">Zum Projekt...</a></p>
    </section>
   </div>
<?php endforeach; ?>

</div>

<div class="divider"></div>

<div class="row">
  <ul class="pagination large-4 columns large-offset-5" style="padding-top:15px;">
    <li class="arrow"><a href="<?= base_path(); ?>Akteure/1">&laquo;</a></li>

    <?php for ($i=1; $i<=$maxPages; $i++) {
     if ($i == $currentPageNr) echo '<li class="current"><a href="#">'.$i.'</a></li>';
     else echo '<li><a href="'.base_path().'Akteure/'.$i.'">'.$i.'</a></li>';
     //<!-- <li class="unavailable"><a href="">&hellip;</a></li>-->
     } ?>

    <li class="arrow"><a href="<?= base_path(); ?>Akteure/<?= $maxPages ?>">&raquo;</a></li>
 </ul>
</div>
