<?php

##########################################################
# Testeley - Testing the Mendeley API
#
# Class: Paper.php
#
# Data class to hold information about a scientific
# publication.
#
# HS 2011-06-14
##########################################################

require_once('functions.inc.php');

class Paper {
	
	### Member variables ###
	var $uuid;		// required
	var $pmid;
	var $title;
	var $authors;
	var $journal;
	var $year;
	var $mendeley_url; 
	
	### Constructor ###
	
	# Create a new paper
	function __construct($uuid){ 
		is_uuid($uuid) or die('invalid uuid');
		$this->uuid = $uuid;
	}
	
	### Class functions ###
			
	# formats the author's array into a string
	# @param with_links if true, add hyperlinks to author last names
	function format_authors($with_links) {
		if(count($this->authors) > 0) {
			$authors_str = "";
			foreach($this->authors as $author) {
				if(isset($author['surname'])) {
					//$surname = htmlentities($author['surname'],ENT_QUOTES,'UTF-8');
					$surname = $author['surname'];
					if(isset($author['forename'])) {
						$forename = htmlentities($author['forename'],ENT_QUOTES,'UTF-8');
						$authors_str = $authors_str.substr($forename,0,1).". ";	
					}
					if($with_links) {
						$authors_str = $authors_str."<a href='author.php?author=".$surname."'>".$surname."</a>";
					} else {
						$authors_str = $authors_str.$surname;
					}
				}
				$authors_str = $authors_str.", ";
			}
			if(strlen($authors_str) > 1) {
				return substr($authors_str,0,-2);
			} else {
				return "Unknown author";
			}
			#return $this->authors[0]['surname'] . " et al.";
		} else {
			return "Unknown author";
		}
	}
	
	# output this citation in html format
	function format_html() {
		echo '<table class="paper">';
		echo '<tr><td class="authors">'.$this->format_authors(true), '</td></tr>';
		echo '<tr><td class="title">'.(isset($this->title)?''.$this->title.'':'Unknown title').'</td></tr>';
		echo '<tr><td class="journal">';
		echo isset($this->journal)? $this->journal: "";
		echo isset($this->year)?' ('.$this->year.')':"";
		echo '</td></tr>';
		echo '<tr><td><div class="paper_actions">';
		if(isset($this->mendeley_url) && is_mendeley_url($this->mendeley_url)) echo '<a class="button" href="',$this->mendeley_url,'"><span>View in Mendeley</span></a> ';
		if(isset($this->pmid) && is_pmid($this->pmid)) echo '<a class="button" href="http://www.ncbi.nlm.nih.gov/pubmed/',$this->pmid,'"><span>View in PubMed</span></a> ';
		echo '<a class="button" href="related.php?uuid=',$this->uuid,'"><span>Related</span></a> ';
		echo '<a class="button" href="details.php?uuid=',$this->uuid,'"><span>Add to database</span></a>';
		echo '</div></td></tr>';
		echo '</table>';
	}
	
	# print a header for the tsv record as returned by format_tsv()
	function write_tsv_header() {
		echo 'uuid\tfirst_author\ttitle\tjournal\tyear\tpmid\n';
	}
	
	# format the paper as a database record in tsv format
	function format_tsv() {
		echo $this->uuid."\t";
		echo (isset($this->authors) && isset($this->authors[0]) && isset($this->authors[0]['surname']))?$this->authors[0]['surname']:"Unknown author";
		echo "\t";
		echo isset($this->title)?$this->title:'Unknown title';
		echo "\t";
		echo isset($this->journal)? $this->journal:"";
		echo "\t";
		echo isset($this->year)?$this->year:"";
		echo "\t";
		echo isset($this->pmid)?$this->pmid:"";
		echo "\t";
		echo "\n";
	}
}

?>
