<?php
 
if (!defined('SMF')) {
	if(file_exists(dirname(__FILE__) . '/SSI.php'))
		include_once(dirname(__FILE__) . '/SSI.php');
	else
		die('<strong>Error</strong>: Please make sure you have SSI.php in your root directory.');
}
 
global $db_prefix, $smcFunc;

db_extend('packages');

$tables = array('item', 'spell', 'quest', 'npc', 'recipe');

$columns = array(
	array(
		'name' => 'id',
		'type' => 'int',
		'size' => 11,
	),
	array(
		'name' => 'name_en',
		'type' => 'varchar',
		'size' => 255,
		'default' => '',
	),
);

$indexes = array(
	array(
		'type' => 'primary',
		'columns' => array('id')
	),
	array(
		'name' => 'name',
		'type' => 'index',
		'columns' => array('name_en')
	),
);

foreach($tables as $table) {
	$smcFunc['db_create_table']($db_prefix . 'aion_' . $table, $columns, $indexes, array('no_prefix' => true), 'ignore');
}
?>