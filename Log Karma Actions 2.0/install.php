<?php

// If SSI.php is in the same place as this file, and SMF isn't defined...
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
  require_once(dirname(__FILE__) . '/SSI.php');

// Hmm... no SSI.php and no SMF?
elseif (!defined('SMF'))
  die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

if ((SMF == 'SSI') && !$user_info['is_admin']) 
    die('Admin privileges required.');

// Install =].
global $smcFunc;

$log_karma_actions = array(
	'table' => '{db_prefix}log_karma_actions',
	'columns' => array(
		0 => array(
			'name' => 'id_karma',
			'type' => 'tinyint',
			'size' => 4,
			'unsigned' => true,
			'null' => false,
			'auto' => true,
		),
		1 => array(
			'name' => 'id_target',
			'type' => 'mediumint',
			'size' => 8,
			'unsigned' => true,
			'null' => false,
			'default' => 0,
		),
		2 => array(
			'name' => 'id_executor',
			'type' => 'mediumint',
			'size' => 8,
			'unsigned' => true,
			'null' => false,
			'default' => 0,
		),
		3 => array(
			'name' => 'log_time',
			'type' => 'int',
			'size' => 10,
			'unsigned' => true,
			'null' => false,
			'default' => 0,
		),
		4 => array(
			'name' => 'action',
			'type' => 'tinyint',
			'size' => 4,
			'unsigned' => false,
			'null' => false,
			'default' => 0,
		)
	),
	'indices' => array(
		0 => array(
				'type' => 'primary',
				'columns' => array('id_karma')
		),
	)
);
	
db_extend('packages');

$smcFunc['db_create_table']($log_karma_actions['table'], $log_karma_actions['columns'], $log_karma_actions['indices'], array(), 'ignore');

$request = $smcFunc['db_query']('', '
	DESCRIBE {db_prefix}log_karma_actions id_karma',
	array()
);

if($smcFunc['db_num_rows']($request) == 0)
{
	$smcFunc['db_free_result']($request);
	
	$smcFunc['db_query']('', '
		ALTER TABLE {db_prefix}log_karma_actions ADD id_karma TINYINT(4)
		UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
		ADD PRIMARY KEY (id_karma)',
		array()
	);
}

?>