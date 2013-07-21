<?php
if (!defined('SMF'))
	die('Hacking attempt...');
	
function BuddyPageMain() {
	global $txt, $context, $scripturl, $sc, $modSettings, $user_info, $user_profile, $settings, $sourcedir, $options, $smcFunc;
	
	isAllowedTo('user_buddypage');
	loadLanguage('Profile');
	$context['page_title'] = $txt['buddypage'];
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=buddypage',
		'name' => $txt['buddypage']
	);
	
	if(!count($user_info['buddies']))
		fatal_lang_error('no_buddies', false);
	
	loadMemberData($user_info['buddies']);
	
	$other = allowedTo('profile_view_any');
	$can_pm = allowedTo('pm_send');
	if ($modSettings['avatar_action_too_large'] == 'option_html_resize' || $modSettings['avatar_action_too_large'] == 'option_js_resize') {
		$avatar_width = !empty($modSettings['avatar_max_width_external']) ? ' width="' . $modSettings['avatar_max_width_external'] . '"' : '';
		$avatar_height = !empty($modSettings['avatar_max_height_external']) ? ' height="' . $modSettings['avatar_max_height_external'] . '"' : '';
	} else {
		$avatar_width = '';
		$avatar_height = '';
	}
	foreach($user_info['buddies'] as $uid) {
		loadMemberContext($uid);
		$context['buddies'][$uid] = array(
			'link' => ($other) ? '<a href="' . $scripturl . '?action=profile;u=' . $uid . '">' . $user_profile[$uid]['real_name'] . '</a>' : $context['user']['name'],
			'avatar' => $user_profile[$uid]['avatar'] == '' ? ($user_profile[$uid]['id_attach'] > 0 ? '<img src="' . (empty($user_profile[$uid]['attachment_type']) ? $scripturl . '?action=dlattach;attach=' . $user_profile[$uid]['id_attach'] . ';type=avatar' : $modSettings['custom_avatar_url'] . '/' . $user_profile[$uid]['filename']) . '" alt="" class="avatar" border="0" />' : '') : (stristr($user_profile[$uid]['avatar'], 'http://') ? '<img src="' . $user_profile[$uid]['avatar'] . '"' . $avatar_width . $avatar_height . ' alt="" class="avatar" border="0" />' : '<img src="' . $modSettings['avatar_url'] . '/' . htmlspecialchars($user_profile[$uid]['avatar']) . '" alt="" class="avatar" border="0" />'),
			'removebuddy' => '<a href="' . $scripturl . '?action=buddy;u=' . $uid . ';' . $context['session_var'] . '=' . $context['session_id'] . '">' . $txt['buddy_remove'] . '</a>',
			'is_online' => $user_profile[$uid]['is_online'],
			'lastlogin' => $txt['lastLoggedIn'] . ': ' . timeformat($user_profile[$uid]['last_login']),
			'buddies_in_common' => allowedTo('user_buddy_seecommon') ? array_intersect($user_info['buddies'], explode(",", $user_profile[$uid]['buddy_list'])) : array(),
			'send_pm' => ($can_pm) ? '<a href="' . $scripturl . '?action=pm;sa=send;u=' . $uid . '">' . $txt['profileSendIm'] . '</a>' : '',
			'has_you' => allowedTo('user_buddy_seeothers') && in_array($user_info['id'], explode(",", $user_profile[$uid]['buddy_list'])),
		);
	}
	
	loadTemplate('BuddyPage');
}

?>