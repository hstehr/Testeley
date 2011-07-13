<?php

##########################################################
# Testeley - Testing the Mendeley API
#
# Class: LitDb.php
#
# Connection class to interact with a local literature 
# database. The database holds record about scientific
# papers of type Paper.php and similarities between
# papers (pairs of uuids). This class provides methods
# to query these tables by uuids (Mendeley unique ids).
#
# Tables:
# doc (uuid, title, authors, journal, year, pmid, mendeley_url)
# sim (uuid1, uuid2) 
#
# HS 2011-06-14
##########################################################

require_once('Paper.php');
require_once('Mendeley.php');
require_once('functions.inc.php');

class LitDb {
	
	### Member variables ###
	var $dbPath;			# path to the sqlite database
	var $db;				# the database handle
	
	### Constructor ###
	
	# Open a new database connection
	function __construct($dbPath){ 
		$this->dbPath = $dbPath;
		try {
		  //create or open the database
		  $this->db = new SQLiteDatabase($dbPath, 0666, $error);
		}
		catch(Exception $e) {
		  die($error);
		}
	}
	
	### Static functions ###
	
	# creates a new sqlite database with the required tables
	# Warning: overwrites existing database
	function create() {
		$query = 'CREATE TABLE '.TBL_PAPERS.
		         ' (uuid TEXT, title TEXT, authors TEXT, journal TEXT, year INTEGER, pmid INTEGER, mendeley_url TEXT);' .
		
				 'CREATE TABLE '.TBL_SIM.
		         ' (uuid1 TEXT, uuid2 TEXT);';
		         
		if(!$this->db->queryExec($query, $error)) {
		  die($error);
		} else {
			echo 'Database created';
		}
	}
	
	# delete all records from the database
	function delete() {
		$query = 'DELETE FROM '.TBL_PAPERS.'; '.
				 'DELETE FROM '.TBL_SIM.';';
		if(!$this->db->queryExec($query, $error)) {
		  die($error);
		} else {
			echo 'Database deleted.';
		}
	}
	
	### Class functions ###
	
	# returns whether this paper is already in the db
	function in_db($uuid) {
		return $this->from_db($uuid);
	}
	
	# stores the given paper record in the db
	function to_db($p) {
		$query = sprintf(
		  "INSERT INTO %s (uuid, title, authors, journal, year, pmid, mendeley_url) " .
		  "VALUES ('%s', '%s', '%s', '%s', %d, %d, '%s'); ", 
		  TBL_PAPERS, 
		  sqlite_escape_string($p->uuid), 
		  sqlite_escape_string($p->title),
		  json_encode($p->authors), 
		  sqlite_escape_string($p->journal), 
		  sqlite_escape_string($p->year), 
		  sqlite_escape_string($p->pmid), 
		  is_mendeley_url($p->mendeley_url)?$p->mendeley_url:"");
		if(!$this->db->queryExec($query, $error)) {
		  echo $query."<br>";
		  die($error);
		} else {
			#echo "<br>".$query;
		}
	}
	
	# retrieve paper from db by uuid
	function from_db($uuid) {
		is_uuid($uuid) or die('invalid uuid');
		$query = "SELECT * FROM ".TBL_PAPERS." WHERE uuid='".$uuid."';";
		if($result = $this->db->query($query, SQLITE_BOTH, $error)) {
		  if($row = $result->fetch()) {
		    $p = new Paper($uuid);
		    $p->title = $row['title'];
		    $p->authors = json_decode($row['authors'],true);
		    $p->journal = $row['journal'];
		    $p->year = $row['year'];
		    $p->pmid = $row['pmid'];
		    $p->mendeley_url = $row['mendeley_url'];
		    return $p;
		  } else {
		  	return false;
		  }
		}
		else {
		  die($error);
		}
	}
	
	# retrieve all papers from db
	# return an array of papers
	function get_all_papers() {
	//read data from database
		$query = "SELECT uuid FROM ".TBL_PAPERS;
		$papers = Array();
		if($result = $this->db->query($query, SQLITE_BOTH, $error)) {
		  while($row = $result->fetch()) {
		  	$uuid = $row['uuid'];
		  	$papers[] = $this->from_db($uuid);
		  }
		  return $papers;
		}
		else {
		  die($error);
		}
	}
	
	# retrieve all similarities from db
	# return an array of pairs
	function get_all_sim() {
		$query = "SELECT * FROM ".TBL_SIM;
		$pairs = Array();
		if($result = $this->db->query($query, SQLITE_BOTH, $error)) {
		  while($row = $result->fetch()) {
		  	if(isset($row['uuid1']) && isset($row['uuid1'])) {
		  		$uuid1 = $row['uuid1'];
		  		$uuid2 = $row['uuid2'];		  	
		  		$pairs[] = array($uuid1,$uuid2);
		  	}
		  }
		  return $pairs;
		}
		else {
		  die($error);
		}
		return $pairs;
	}
	
	# retrieve similar documents for the given uuid
	# returns an array of uuids
	function get_related_from_db($uuid) {
		is_uuid($uuid) or die('invalid uuid');
		$query = "SELECT uuid2 FROM ".TBL_SIM." WHERE uuid1='.$uuid.'";
		$uuids = Array();
		if($result = $this->db->query($query, SQLITE_BOTH, $error)) {
		  while($row = $result->fetch()) {
		  	if(isset($row['uuid']) && is_uuid($row['uuid']))
		  		$uuids[] = $row['uuid'];
		  }
		} else die($error);
		return uuids;		
	}
	
	# returns whether this similarity is already in the db
	function sim_in_db($uuid1, $uuid2) {
		is_uuid($uuid1) or die('invalid uuid1');
		is_uuid($uuid2) or die('invalid uuid2');
		$query = sprintf("SELECT uuid1, uuid2 FROM %s WHERE uuid1='%s' AND uuid2='%s';", TBL_SIM, $uuid1, $uuid2);
		if($result = $this->db->query($query, SQLITE_BOTH, $error)) {
		  if($row = $result->fetch()) return true;
		  else 	return false;
		} else {
		  die($error);
		}
	}
	
	# adds a similarity relation between two papers to the db
	# does nothing if the relation already exists
	function sim_to_db($uuid1, $uuid2) {
		is_uuid($uuid1) or die('invalid uuid1');
		is_uuid($uuid2) or die('invalid uuid2');
		if(!$this->sim_in_db($uuid1, $uuid2)) { 
			$query = sprintf("INSERT INTO %s (uuid1, uuid2) VALUES ('%s', '%s');",TBL_SIM, $uuid1,$uuid2);
			if(!$this->db->queryExec($query, $error)) {
			  die($error);
			}
		}
	}
	
	/**
	 * Returns the number of papers in the local database.
	 * Enter description here ...
	 */
	function get_num_papers() {
		return count($this->get_all_papers());		
	}
	
	/**
	 * Returns the number of similarities in the local database.
	 */
	function get_num_sim() {
		return count($this->get_all_sim());
	}
	
	/**
	 * Prints the number of papers and similarities.
	 */
	function print_db_stats() {
		echo 'Documents: '.$this->get_num_papers();
		echo '<br>';
		echo 'Similarities: '.$this->get_num_sim();
		echo '<br>';
	}

	/**
	 * Find for all papers in the local database the related papers and add them to the database.
	 * Warning: This function does a lot of API calls.
	 */
	function expand() {
		$mendeley = new Mendeley(API_KEY);
		$docs_in_db = $this->get_all_papers();
		foreach($docs_in_db as $doc) {
			$uuid1 = $doc->uuid;
			$uuids = $mendeley->get_related_uuids($uuid1);
			foreach($uuids as $uuid2) {
				if(!$this->in_db($uuid2)) {
					$paper2 = $mendeley->get_document($uuid2);
					$this->to_db($paper2);
				}
				if(!$this->sim_in_db($uuid1, $uuid2)) {
					$this->sim_to_db($uuid1, $uuid2);
					//echo '.'; 
					//flush();
				}
			}
			//echo '<br>';
			//flush();
		}
	}
}


?>
