<?php
/**************************************************************************
* Topic Locked Message											*
***************************************************************************/

// Manual Install?
// If the user is manually installing allow them to upload install.php 
// providing its in the same directory as SMF, allow the install to proceed, 
if(!defined('SMF'))
	include_once('SSI.php');

// Make sure we have access to install packages
if(!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

// Globals
global $db_prefix;

// Columns for our table
$column_info = array('name' => 'topicLockedMessage', 'type' => 'TINYINT', 'size' => 1, 'null' => false, 'default' => 0);

// Create the table
$smcFunc['db_add_column']('{db_prefix}messages', $column_info, '', '', '');

// If we're using SSI, tell them we're done
if(SMF == 'SSI')
	echo 'Database changes are complete!';

?>