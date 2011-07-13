<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: group.php
#
# Takes a group id and displays the contained documents.
#
# HS 2011-06-15
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('Mendeley.php');

$mendeley = new Mendeley(API_KEY);

if(isset($_GET['gid']) && $_GET['gid'] != "") {
	$gid = $_GET['gid'];
	is_mendeley_group_id($gid) or die('invalid gid');
	
	if(isset($_GET['format']) && $_GET['format'] == "raw") {
		$json = $mendeley->fetch_docs_in_group($gid);
		format_json($json);		
	} else {	
		$uuids = $mendeley->get_uuids_in_group($gid);
		if(count($uuids) == 0) {
			echo 'No documents found';
			link_home();
		} else {
			html_header();
			link_home();
			echo '<h3>Documents in group '.$gid.'</h3>';
			foreach($uuids as $uuid) {
				$paper = $mendeley->get_document($uuid);
				echo '<P>';
				$paper->format_html();
				echo '</P>';
			}
			html_footer();
		}
	}

} else {
	echo 'No GID given.';
}

?>