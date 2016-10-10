<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
    <title>Leipziger Ecken RSS Feed</title>
    <link>https://www.leipziger-ecken.de</link>
    <atom:link href="https://www.leipziger-ecken.de/events/rss" rel="self" type="application/rss+xml" />
    <description>Alle kommenden Events im Leipziger Osten, powered by Leipziger Ecken.</description>
    <language>de-de</language>
    <pubDate><?php $rNow = new DateTime('01.01.2016'); echo $rNow->format(DateTime::RFC822); ?></pubDate>
    <lastBuildDate><?php $rNow = new DateTime('NOW'); echo $rNow->format(DateTime::RFC822); ?></lastBuildDate>
    <docs>https://www.leipziger-ecken.de/events/rss</docs>
    <generator>AAE Data</generator>
    <managingEditor>info@leipziger-ecken.de</managingEditor>
    <webMaster>info@leipziger-ecken.de</webMaster>
    <image>
     <url>https://leipziger-ecken.de/<?= path_to_theme(); ?>/logo.png</url>
     <title>Leipziger Ecken RSS Feed</title>
     <link>https://leipziger-ecken.de/events/rss</link>
    </image>

<?php foreach ($resultEvents as $event) : ?>
<?php # $start = new DateTime($event->start_ts);
      #$created = new DateTime($event->created); ?>
    <item>
     <title><?= htmlspecialchars($event->name); ?> <?= t('am'); ?> <?= $event->start->format('d.m.Y'); ?></title>
     <link>https://leipziger-ecken.de/eventprofil/<?= $event->EID; ?></link>
     <description><?= htmlspecialchars($event->kurzbeschreibung); ?></description>
     <category>Event</category>
     <pubDate><?= $event->created->format(DateTime::RFC822); ?></pubDate>
    </item>
<?php endforeach; ?>

</channel>
</rss>
