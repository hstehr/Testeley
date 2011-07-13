<?php

##########################################################
# Testeley - Testing the Mendeley API
#
# Class: Mendeley.php
#
# Connection class to query information from the
# Mendeley API. 
#
# HS 2011-06-14
##########################################################

require_once('Paper.php');
require_once('functions.inc.php');

class Mendeley {
	
	### Member variables ###
	var $consumer_key;
	
	# Open a new Mendeley connection
	function __construct($consumer_key){ 
		$this->consumer_key = $consumer_key;
	}
	
	### Class functions ###
	
	# fetches documents in a public group
	# returns the raw json response
	function fetch_docs_in_group($group_id) {
		  $api_docs_in_group="http://api.mendeley.com/oapi/documents/groups/%s/docs/?details=true&consumer_key=%s";
		  $url=sprintf($api_docs_in_group,$group_id,$this->consumer_key);
		  $json=file_get_contents($url);
		  return $json;		
	}
	
	# get all documents in a public group
	# returns an array of uuids
	function get_uuids_in_group($group_id) {
		  $json = $this->fetch_docs_in_group($group_id);
		  $res=json_decode($json,true);
		  $uuids = Array();
		  if(isset($res["documents"])) {
			  foreach($res["documents"] as $doc) {
			    if(isset($doc["uuid"]) && is_uuid($doc["uuid"])) {
			    	$uuids[] = $doc["uuid"];
			    	#var_dump($doc["uuid"]);
			    }
			  }
		  }
		  #var_dump($uuids);
		  #echo "<BR>";
		  return $uuids;
	}
	
	# fetch document details from Mendeley by uuid
	# returns the raw json response
	function fetch_details($uuid) {
		$api="http://api.mendeley.com/oapi/documents/details/%s/?consumer_key=%s";
  		$url=sprintf($api,$uuid,$this->consumer_key);
  		$json=file_get_contents($url);
		return $json;
	}
	
	# retrieves a document by its uuid
	function get_document($uuid) {
		$json = $this->fetch_details($uuid);
		$res=json_decode($json,true);
  		$paper = new Paper($uuid);
  		if(isset($res["authors"])) $paper->authors = $res["authors"];
  		if(isset($res["title"])) $paper->title = htmlentities($res["title"]);
  		if(isset($res["year"])) $paper->year = htmlentities($res["year"]);
		if(isset($res["publication_outlet"])) $paper->journal = htmlentities($res["publication_outlet"]);
		if(isset($res['mendeley_url']) && is_mendeley_url($res['mendeley_url'])) $paper->mendeley_url = $res['mendeley_url'];
		if(isset($res['identifiers'])) {
			if(isset($res['identifiers']['pmid']) && is_pmid($res['identifiers']['pmid'])) $paper->pmid = $res['identifiers']['pmid'];
		}
		return $paper;
	}
	
	# fetches related documents for a given uuid.
	# returns the raw json response
	function fetch_related($uuid) {
		$api="http://api.mendeley.com/oapi/documents/related/%s/?consumer_key=%s";
		$url=sprintf($api,$uuid,$this->consumer_key);
  		$json=file_get_contents($url);
  		return $json;		
	}
	
	# get related documents from Mendeley by uuid
	# returns an array of uuids
	function get_related_uuids($uuid) {
		$json = $this->fetch_related($uuid);
		$res=json_decode($json,true);
		$uuids = Array();
		foreach($res["documents"] as $doc) {
		    if(isset($doc["uuid"]) && is_uuid($doc["uuid"])) {
		    	$uuids[] = $doc["uuid"];
		    }
		  }
		return $uuids;
	}
	
	# fetch papers by author name
	# returns the raw json response
	function fetch_docs_by_author($author) {
		$api="http://api.mendeley.com/oapi/documents/authored/%s/?consumer_key=%s";
		$url=sprintf($api,$author,$this->consumer_key);
  		$json=file_get_contents($url);
  		return $json;
	}
	
	# get papers by author name
	# returns an array of uuids
	function get_uuids_by_author($author) {
		$json = $this->fetch_docs_by_author($author);
		$res=json_decode($json,true);
		$uuids = Array();
		foreach($res["documents"] as $doc) {
		    if(isset($doc["uuid"]) && is_uuid($doc["uuid"])) {
		    	$uuids[] = $doc["uuid"];
		    }
		  }
		return $uuids;
	}
	
}
?>