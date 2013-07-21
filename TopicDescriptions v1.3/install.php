<?php

if (!defined('SMF'))
	die('Hacking attempt...');

global $smcFunc;

db_extend('packages');

$smcFunc['db_add_column']("{db_prefix}topics",
	array(
        	'name' => 'description', 
        	'type' => 'text', 
        	'null' => true,
        ), array(), 'do_nothing', 'fatal'
);

// RC5 bug aw man :(

/*$hooks = array(
	'integrate_modify_modifications' => 'integrateTopicdescSettings',
);

foreach ($hooks as $hook => $function)
	add_integration_function($hook, $function);*/

?>