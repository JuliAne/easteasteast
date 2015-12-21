
<rss version="2.0">
<channel>
    <title>Leipziger Ecken</title>
    <link>https://www.leipziger-ecken.de</link>
    <description>(D)eine Stadtteilplattform für den Leipziger Osten</description>
    <language>de-de</language>
    <pubDate>01.01.2016</pubDate>
    <lastBuildDate><?= date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000)); ?></lastBuildDate>
    <docs>https://www.leipziger-ecken.de/events/rss</docs>
    <generator>AAE Data</generator>
    <managingEditor>info@leipziger-ecken.de</managingEditor>
    <webMaster>info@leipziger-ecken.de.de</webMaster>

<?php foreach ($resultEvents as $event) : ?>
<?php $explodeDate = explode("-",$event->start); ?>
    <item>
    <title><?= htmlspecialchars($event->name); ?> am <?= $explodeDate[0].'.'.$explodeDate[1].'.'.$explodeDate[2]; ?></title>
    <link>https://leipziger-ecken.de<?= base_path(); ?>eventprofil/<?= $event->EID; ?></link>
    <description>
    <?= htmlspecialchars($this->kurzbeschreibung); ?>
    </description>
    <category>Event</category>
    <image><?= $event->bild; ?></image>
    </item>
<?php endforeach; ?>

</channel>
</rss>
