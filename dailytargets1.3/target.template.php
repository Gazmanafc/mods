<?php

function template_dailyt_above()
{	
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $sourcedir, $new_posts;
	if (!empty($modSettings['daily_target_control']))
   	{
   		   	echo '
			<span>', $txt['daily_target1'],' ', $new_posts, ' ', $txt['daily_target2'],' ', !empty($modSettings['daily_target']) ? ''. parse_bbc($modSettings['daily_target']). '' : '', ' ', $txt['daily_target3'],'</span>';					
   	}
}

function template_dailyt_below()
{}
?>