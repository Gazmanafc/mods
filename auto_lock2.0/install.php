<?php
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
else if(!defined('SMF'))
	die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php and SSI.php files.');

if ((SMF == 'SSI') && !$user_info['is_admin'])
	die('Admin priveleges required.');

// All this for one query...

$query = $smcFunc['db_query']('', '
	SELECT id_task FROM {db_prefix}scheduled_tasks WHERE task = "autoLock"
');

if ($smcFunc['db_num_rows']($query) == 0)
	$smcFunc['db_query']('', '
		INSERT INTO {db_prefix}scheduled_tasks (id_task, next_time, time_offset, time_regularity, time_unit, disabled, task)
		VALUES (NULL, 0, 0, 1, "d", 0, "autoLock")'
	);

if (SMF == 'SSI')
	echo 'Database changes are complete!';
?>