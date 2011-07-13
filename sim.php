<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: sim.php
#
# Shows the contents of the similarity table in the
# local database.
#
# HS 2011-07-06
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('LitDb.php');

$db = new LitDb(DB_PATH);

$sims = $db->get_all_sim();

if(isset($_GET['format']) && $_GET['format'] == "tsv") {
	header('Content-type: text/plain');
	//header('Content-Disposition: attachment; filename="edges.txt"');
	foreach($sims as $s) {
		echo $s[0]."\t".$s[1]."\n";
	}	
} else {
	html_header();
	link_home();	
	if(count($sims) > 0) {
		echo '<h3>Similarity relations in local database: '.count($sims).'</h3>';
		foreach($sims as $s) {
			$u1 = $s[0];
			$u2 = $s[1];
			echo "<a href='details.php?uuid={$u1}'>{$u1}</a> -> <a href='details.php?uuid={$u2}'>{$u2}</a>";
			echo '<br />';
		}
	} else {
		echo 'No records found.';
	}
	html_footer();	
}

?>