<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: details.php
#
# Takes a UUID and displays the document details.
#
# HS 2011-06-15
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('Mendeley.php');
require_once('LitDb.php');

$mendeley = new Mendeley(API_KEY);

if(isset($_GET['uuid']) && $_GET['uuid'] != "") {
	$uuid = $_GET['uuid'];
	is_uuid($uuid) or die('invalid uuid');
	
	if(isset($_GET['format']) && $_GET['format'] == "raw") {
		$json = $mendeley->fetch_details($uuid);
		format_json($json);
	} else {
		html_header();
		link_home();
		$paper = $mendeley->get_document($uuid);
		$paper->format_html();
		$db = new LitDb(DB_PATH);
		if($db->in_db($paper->uuid)) {
			echo '</p>Paper already in DB</p>';
		} else {
			$db->to_db($paper);
			echo '<p>Paper added to DB</p>';
		}
		html_footer();
	}
	
} else {
	echo 'No UUID given.';
}

?>