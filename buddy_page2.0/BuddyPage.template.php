<?php

function template_main() {
	global $context, $txt, $user_info, $user_profile, $context, $settings;
	
	echo '
	<table class="table_list">
		<tbody class="header">
			<tr>
				<td colspan="2" class="catbg">
					<span class="left"></span>
					', $txt['buddies'], '
				</td>
			</tr>
		</tbody>
		<tbody class="content">';
	
	foreach($context['buddies'] as $user_id => $buddy) {
		echo '			<tr class="windowbg2">
				<td width="20%" align="center">', $buddy['avatar'], '</td>
				<td>
					';

		echo '<table width="100%" cellspacing="8">';
		echo '<tr><td width="5%" align="center"><img src="', $settings['images_url'], ($buddy['is_online'] ? '/online.gif' : '/offline.gif'), '" alt="', ($buddy['is_online'] ? $txt['online'] : $txt['offline']) , '" title="', ($buddy['is_online'] ? $txt['online'] : $txt['offline']) , '" /></td><td><h4>', $buddy['link'], '</h4></td><td width="30%" rowspan="5" valign="top"><b>', $txt['buddiesincommon'], '</b>:<br />';
		if(count($buddy['buddies_in_common']) == 0) {
			echo $txt['none'];
		} else {
			$list = array();
			foreach($buddy['buddies_in_common'] as $common) {
				$list[] = $context['buddies'][$common]['link'];
			}
			echo implode(', ', $list);
		}
		
		echo '</td></tr><tr><td align="center"><img src="', $settings['images_url'], '/icons/package_installed.gif" alt="" /></td><td>', $buddy['lastlogin'], '</td></tr>';
		
		if(!empty($buddy['send_pm']))
			echo '<tr><td align="center"><img src="', $settings['images_url'], '/email_sm.gif" alt="" /></td><td>', $buddy['send_pm'], '</td></tr>';
		
		echo '<tr><td align="center"><img src="', $settings['images_url'], '/buttons/delete.gif" alt="" /></td><td>', $buddy['removebuddy'], '</td></tr>';
		
		if($buddy['has_you'])
			echo '<tr><td align="center"><img src="', $settings['images_url'], '/icons/field_valid.gif" alt="" /></td><td>', $txt['user_has_you_buddy'], '</td></tr>';

		echo '</table>
				</td>
			</tr>';
	}
	echo '
		</tbody>
	</table>';
}

?>