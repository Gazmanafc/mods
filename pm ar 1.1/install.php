<?php

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
{
	$ssi = true;
	require_once(dirname(__FILE__) . '/SSI.php');
}
elseif (!defined('SMF'))
	exit('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');

add_integration_function('integrate_pre_include', '$sourcedir/PMAutoResponder.php');
add_integration_function('integrate_personal_message', 'pm_ar_personal_message');
add_integration_function('integrate_profile_areas', 'pm_ar_profile_areas');

if (!empty($ssi))
	echo 'Database installation complete!';

?>