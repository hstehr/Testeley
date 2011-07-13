<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: related.php
#
# Takes a uuid and displays related documents.
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
		$json = $mendeley->fetch_related($uuid);
		format_json($json);	
	} else {
		html_header();
		$uuids = $mendeley->get_related_uuids($uuid);
		if(count($uuids) == 0) {
			echo 'No documents found';
			link_home();
		} else {
			$paper = $mendeley->get_document($uuid);
			$db = new LitDb(DB_PATH);
			if(!$db->in_db($uuid)) $db->to_db($paper);
			link_home();
			echo '<h3>Related documents for '.$uuid.'</h3>';
			foreach($uuids as $uuid2) {
				$paper2 = $mendeley->get_document($uuid2);
				if(!$db->in_db($uuid2)) $db->to_db($paper2);
				if(!$db->sim_in_db($uuid, $uuid2)) $db->sim_to_db($uuid, $uuid2);	
				echo '<P>';
				$paper2->format_html();
				echo '</P>';
			}
		}
		html_footer();
	}	
} else {
	echo 'No UUID given.';
}

?>