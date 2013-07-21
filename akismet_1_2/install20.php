<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

add_integration_function('integrate_pre_include', '$sourcedir/Subs-Akismet.php');
add_integration_function('integrate_load_theme', 'akismet_load_theme');
add_integration_function('integrate_modify_modifications', 'akismet_modify_modifications');
add_integration_function('integrate_admin_areas', 'akismet_admin_areas');
add_integration_function('integrate_create_topic', 'akismet_create_topic');

if (!array_key_exists('db_add_column', $smcFunc))
	db_extend('packages');

$column = array(
	'name' => 'spam',
	'type' => 'tinyint',
	'size' => 1,
);

$smcFunc['db_add_column']('{db_prefix}topics', $column);

updateSettings(array(
	'akismetCaughtSpam' => 0,
	'akismetAPIKey' => '',
));

if (!empty($ssi))
	echo 'Database installation complete!';

?>