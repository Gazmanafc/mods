<?php

/*******************************************************************************
	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	Last Edit Link Version 2.0

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
	$standalone = true;
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s SSI.php.');

//	Make sure that we have the package database functions.
if(!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

// Simply add the new database column
$smcFunc['db_add_column']('{db_prefix}messages', array(
        'name' => 'modified_id',
        'type' => 'mediumint',
        'size' => 8,
        'default' => 0,
        'unsigned' => true,
    )
);

?>