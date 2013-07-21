<?php

/*******************************************************************************
	ATTENTION: If you are trying to install this manually, you should try
	the package manager. If that does not work, you can run this file by
	accessing it by url. Please ensure it is in the same location as your
	forum's SSI.php file.
*******************************************************************************/

//	eNinja - Admin Notes Version 0.9.2

//	If SSI.php is in the same place as this file, and SMF isn't defined, this is being run standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	require_once(dirname(__FILE__) . '/SSI.php');
	$standalone = true;
}
//	Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot uninstall - please verify you put this in the same place as SMF\'s SSI.php.');

// Simply remove all notes
$smcFunc['db_query']('', '
    DELETE FROM {db_prefix}log_comments
    WHERE comment_type = {string:type}',
    array(
        'type' => 'a_note',
    )
);

// Remove from the cache as well!
cache_put_data('admin_notes', null, 240);
cache_put_data('admin_notes_total', null, 240);

?>
