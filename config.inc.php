<?php

##########################################################
# Testeley - Testing the Mendeley API
#
# File: config.inc.php
#
# Global configuration settings. Adjust these to your
# local environment.
#
# HS 2011-06-15
##########################################################

# Path to sqlite database (will be created)
define('DB_PATH',"/path/to/db.sqlite");

# Database table names
define('TBL_PAPERS','doc');		# name of the 'papers' table
define('TBL_SIM','sim');		# name of the 'similarities' table

# Mendeley consumer key for public API calls
define('API_KEY',"your_api_key");

?>
