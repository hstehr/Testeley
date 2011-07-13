<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: db.php
#
# Shows the contents of the literature database. If the
# database does not already exist, it is created.
#
# HS 2011-06-29
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('LitDb.php');

$db = new LitDb(DB_PATH);

if(isset($_GET['action']) && $_GET['action'] == "create") {
	html_header();
	link_home();		
	$db->create();
	html_footer();
} elseif(isset($_GET['action']) && $_GET['action'] == "delete") {
	html_header();
	link_home();		
	$db->delete();
	html_footer();
} else {
	$papers = $db->get_all_papers();
	if(isset($_GET['format']) && $_GET['format'] == "tsv") {
		header('Content-type: text/plain');
		//header('Content-Disposition: attachment; filename="nodes.txt"');
		foreach($papers as $p) {
			$p->format_tsv();
		}		
	} else {
		html_header();
		link_home();		
		if(count($papers) > 0) {
			echo '<h3>Papers in local database: '.count($papers).'</h3>';
			foreach($papers as $p) {
				echo '<P>';
				$p->format_html();
				echo '</P>';
			}
		} else {
			echo 'No records found.';
		}
		html_footer();
	}
}

?>