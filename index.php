<?php

##########################################################
# Testeley - Testing the Mendeley API
# HS 2011-06-05
##########################################################

### Constants ###
define('AGLAPPE', '1149541');
define('TESTELEY', '1201641');
define('BIOWIKI', '644361');

include_once('config.inc.php');
include_once('LitDb.php');

?>

<html>
<head>
<link href="main.css" media="all" rel="stylesheet" type="text/css" />
<title>Testeley</title>
</head>
<body>
<?php
echo "<h1>Testeley - Testing the Mendeley API</h1>";
echo '<h2>Group Functions</h2>';
echo '<p>';
echo '<a href="http://www.bifx.de/testeley/group.php?gid=',AGLAPPE,'">Papers in group AGLappe</a><br>';
echo '<a href="http://www.bifx.de/testeley/group.php?gid=',BIOWIKI,'">Papers in group BioWiki</a><br>';
echo '<a href="http://www.bifx.de/testeley/group.php?gid=',TESTELEY,'">Papers in group Testeley</a><br>';
echo '</p>';
echo '<h2>Local Database</h2>';
try {
	$db = new LitDb(DB_PATH);
	echo '<p>';
	$db->print_db_stats();
	echo '</p>';
} catch(Exception $e) {
}
echo '<p>';
echo '<a href="http://www.bifx.de/testeley/db.php">Papers in local database</a> (<a href="http://www.bifx.de/testeley/db.php?format=tsv">Download as tsv</a>)<br />';
echo '<a href="http://www.bifx.de/testeley/sim.php">Similarities in local database</a> (<a href="http://www.bifx.de/testeley/sim.php?format=tsv">Download as tsv</a>)<br />';
echo '<a href="http://www.bifx.de/testeley/expand.php">Find related for all papers</a> (fetch related articles for all papers in <i>documents</i> table)<br />';
echo '<a href="http://www.bifx.de/testeley/db.php?action=delete">Reset database</a> (deletes all entries)';
echo '<br />';
echo '</p>';
echo '<h2>Help</h2>';
echo '<ul class="help">';
echo '<li>To get started, choose one of the groups under <i>Group Functions</i>';
echo '<li>Documents are retrieved online via the Mendeley API';
echo "<li>The local database has two tables: <i>documents</i> (=Nodes) and <i>similarities</i> (=Edges)";
echo "<li>Documents can be added to the database with the <i>Add to database</i> button";
echo "<li>Mendeley has a function to show related articles for a given paper";
echo "<li>Viewing related articles (with the <i>Related</i> button) will automatically add the similarity relationships to the database";
echo "<li>Every related article will also automatically be added to the <i>documents</i> table such that for every edge, the connected nodes are also in the database";
echo "<li><i>Find related for all papers</i> will find related articles for each paper in the <i>documents</i> table. This may take a long time and will submit many Mendeley API calls";
echo "<li>The number of allowed Mendeley API calls per hour is limited, especially the function above may reach this limit very fast if the number of papers in <i>documents</i> is too large (>20)";
echo "<li>Thank you for alpha testing ;)";
echo '</ul>';

?>
</body>
</html>
