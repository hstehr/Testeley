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
		$this->uuid = $uuid;
	}
	
	### Class functions ###
		
	## database functions
	
	# load paper from db
	function read_from_db() {
		// TODO	
	}

	# write paper to db
	function write_to_db() {
		// TODO
	}
	
	## general functions
	
	# formats the author's array into a string
	function format_authors() {
		if(count($this->authors) > 0) {
			return $this->authors[0]['surname'] . " et al.";
		} else {
			return "Unknown author";
		}
	}
	
	# output this citation in html format
	function format_html() {
		echo $this->format_authors(), '<BR>';
		#var_dump($this->authors); echo '<BR>';
		echo isset($this->title)?'"'.$this->title.'"<BR>':'Unknown title<BR>';
		echo isset($this->title)? $this->journal: "";
		echo isset($this->year)?' ('.$this->year.')':"";
		echo '<BR>';
		#echo $this->uuid, '<BR>';
		#echo $this->mendeley_url, '<BR>';
		if(isset($this->mendeley_url)) echo '<a href="',$this->mendeley_url,'">View in Mendeley</a> ';
		if(isset($this->pmid)) echo '<a href="http://www.ncbi.nlm.nih.gov/pubmed/',$this->pmid,'">View in PubMed</a> ';
		echo '<a href="related.php?uuid=',$this->uuid,'">Related</a>';
	}
}

?>
