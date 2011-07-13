<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: author.php
#
# Takes a uuid and displays related documents.
#
# HS 2011-06-28
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('Mendeley.php');

$mendeley = new Mendeley(API_KEY);

if(isset($_GET['author']) && $_GET['author'] != "") {
	$author = $_GET['author'];
	if(strlen($author) > 30 || strpos($author,"<") !== false || strpos($author,">") !== false) die('invalid author');
	
	if(isset($_GET['format']) && $_GET['format'] == "raw") {
		$json = $mendeley->fetch_docs_by_author($author);
		format_json($json);	
	} else {
		$uuids = $mendeley->get_uuids_by_author($author);
		if(count($uuids) == 0) {
			echo 'No documents found';
			link_home();
		} else {
			html_header();
			echo '<h3>Documents for author '.$author.'</h3>';
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
	echo 'No AUTHOR given.';
}

?>