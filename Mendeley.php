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

include_once('Paper.php');

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
			    if(isset($doc["uuid"]) && strlen($doc["uuid"]) == 36) {
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
  		if(isset($res["title"])) $paper->title = $res["title"];
  		if(isset($res["year"])) $paper->year = $res["year"];
		if(isset($res["publication_outlet"])) $paper->journal = $res["publication_outlet"];
		if(isset($res['mendeley_url'])) $paper->mendeley_url = $res['mendeley_url'];
		if(isset($res['identifiers'])) {
			if(isset($res['identifiers']['pmid'])) $paper->pmid = $res['identifiers']['pmid'];
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
		foreach($res["documents"] as $doc) {
		    if(isset($doc["uuid"]) && strlen($doc["uuid"]) == 36) {
		    	$uuids[] = $doc["uuid"];
		    	#var_dump($doc["uuid"]);
		    }
		  }
		return $uuids;
	}
}
?>