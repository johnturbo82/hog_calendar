<h2>Support</h2>
<p>Anwendung mit Copyright von Oliver Schöttner unter <a href="https://github.com/johnturbo82/hog_calendar/blob/main/LICENSE" target="_blank">MIT Lizenz</a>.</p>
<p>Installierte Version: <?php echo CURRENT_VERSION ?></p>
<p>Bitte bei Supportanfragen Versionsnummer mit angeben.</p>
<p>Bei Fragen oder Anregungen bitte E-Mail an <a href="mailto:<?php echo SUPPORT_EMAIL ?>?subject=<?php echo SITE_ADDRESS . " - Version " . CURRENT_VERSION ?>"><?php echo SUPPORT_EMAIL ?></a></p>
<h3>README</h3>
<pre>
<?php
$fh = fopen("README.md", "r");
while ($line = fgets($fh)) {
    echo $line;
}
fclose($fh);
?>
</pre>
<a href="https://github.com/johnturbo82/hog_calendar">Mehr auf Github</a>