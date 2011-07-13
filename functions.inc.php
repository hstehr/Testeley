<?php

##########################################################
# Testeley - Testing the Mendeley API
#
# File: functions.inc.php
#
# Collection of general-purpose helper functions.
#
# HS 2011-06-20
##########################################################

/**
 * Writes a simple HTML header
 */
function html_header() {
	echo '<html><head><link href="main.css" media="all" rel="stylesheet" type="text/css" /></head><body>';
}

/**
 * Writes a simple HTML footer
 */
function html_footer() {
	echo '</body></html>';	
}

function link_home() {
	echo '<p><a href=".">Back to main page</a></p>';
}

/**
 * Checks whether the given string is a valid Mendeley document unique id.
 */
function is_uuid($str) {
	return preg_match('/^[a-z0-9-]{36}$/',$str);
}

/**
 * Checks whether the given string is a valid Mendeley document URL.
 */
function is_mendeley_url($str) {
	return preg_match('#^http://www.mendeley.com/research/[a-z0-9-]+/$#',$str);
}

/**
 * Checks whether the given string is a valid Mendeley group id.
 */
function is_mendeley_group_id($str) {
	return preg_match('/^[0-9]{1,10}$/',$str);
}

/**
 * Checks whether the given string is a valid PubMed id.
 */
function is_pmid($str) {
	return preg_match('/^[0-9]{1,10}$/',$str);
}

/**
 * Strips dangerous characters from strings to prevent
 * SQL injection and script injection attacks.
 * Source: http://www.tech-evangelist.com/2007/11/05/preventing-sql-injection-attack/
 * @param string $string
 */
function secure_string($string) {
	if(get_magic_quotes_gpc()) {	// prevents duplicate backslashes
		$string = stripslashes($string);
	}
	if (phpversion() >= '4.3.0') {	//if using new version of PHP and mysql_real_escape_string
		$string = mysql_real_escape_string(htmlentities($string, ENT_QUOTES));
	} else {	//for the old version of PHP and mysql_escape_string
		$string = mysql_escape_string(htmlentities($string, ENT_QUOTES));
	}
	return $string; //return the secure string
}

/**
 * Mimics the mysql_escape_string function because it was not working on our server.
 */
function mysql_escape_mimic($inp) {
    if(is_array($inp))
        return array_map(__METHOD__, $inp);

    if(!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }

    return $inp;
}

/**
 * Formats a raw json string as HTML.
 * @param string $json The json string to process
 */
function format_json($json) {
	echo '<pre>';
	echo indent_json($json);
	echo '</pre>';
}

/**
 * Indents a flat JSON string to make it more human-readable.
 * Source: http://recursive-design.com/blog/2008/03/11/format-json-with-php/
 * @param string $json The original JSON string to process.
 * @return string Indented version of the original JSON string.
 */
function indent_json($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;
        
        // If this character is the end of an element, 
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element, 
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }
            
            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }
        
        $prevChar = $char;
    }

    return $result;
}
		
?>