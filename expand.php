<?php 

##########################################################
# Testeley - Testing the Mendeley API
#
# Script: expand.php
#
# Find for all papers in the local database the related
# papers via the Mendeley API.
#
# HS 2011-06-15
##########################################################

require_once('config.inc.php');
require_once('functions.inc.php');
require_once('LitDb.php');

apache_setenv('no-gzip', '1');
html_header();
link_home();

$db = new LitDb(DB_PATH);
?>
<h3>Before</h3>
<?php
$db->print_db_stats();
$db->expand();
?>
<h3>After</h3>
<?php
$db->print_db_stats();
html_footer();
?>