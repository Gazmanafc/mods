<?php
// Version: 1.0: PMAutoResponder.php

if (!defined('SMF'))
	die('Hacking attempt...');

function pm_ar_personal_message($recipients, $from_name, $subject, $message)
{
	global $context, $smcFunc, $txt, $user_info;

	if (isset($context['ar_pm']))
		return;

	loadLanguage('PMAutoResponder');
	if (!empty($recipients['to']) || !empty($recipients['bcc']))
	{
		$auto_recipients = array_merge($recipients['to'], $recipients['bcc']);
		foreach ($auto_recipients as &$auto_recipient)
			$auto_recipient = (int) $auto_recipient;

		$request = $smcFunc['db_query']('', '
			SELECT m.id_member, m.real_name, m.member_name, t.value, t.variable
			FROM {db_prefix}members AS m
				INNER JOIN {db_prefix}themes AS t ON (m.id_member = t.id_member AND t.variable LIKE {string:auto_recipients_var})
			WHERE m.id_member IN ({array_int:auto_recipients})
				AND t.value != ""',
			array(
				'auto_recipients' => $auto_recipients,
				'auto_recipients_var' => '%ar_pm_%',
			)
		);
		$members = array();
		$theme_members = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$members[$row['id_member']] = array(
				'name' => $row['real_name'],
				'username' => $row['member_name'],
			);
			$theme_members[$row['id_member']][$row['variable']] = $row['value'];
		}

		foreach ($members as $id_member => $member)
		{
			if (isset($theme_members[$id_member]['ar_pm_enabled'], $theme_members[$id_member]['ar_pm_subject'], $theme_members[$id_member]['ar_pm_body']))
			{
				$context['ar_pm'] = true;
				sendpm(
					array(
						'to' => array($user_info['id']),
						'bcc' => array()
					),
					$theme_members[$id_member]['ar_pm_subject'],
					$theme_members[$id_member]['ar_pm_body'],
					!empty($theme_members[$id_member]['ar_pm_outbox']),
					array(
						'id' => $id_member,
						'name' => $member['name'],
						'username' => $member['username']
					)
				);
			}
		}
	}
}

function pm_ar_profile_areas(&$profile_areas)
{
	global $txt;

	loadLanguage('PMAutoResponder');
	$profile_areas['edit_profile']['areas']['pm_ar'] = array(
		'label' => $txt['ar_pm_profile_area'],
		'file' => 'PMAutoResponder.php',
		'function' => 'PMAutoResponderProfile',
		'enabled' => allowedTo(array('profile_extra_own', 'profile_extra_any')),
		'sc' => 'post',
		'permission' => array(
			'own' => array('profile_extra_own'),
			'any' => array('profile_extra_any'),
		),
	);
}

function PMAutoResponderProfile($memID)
{
	global $context, $cur_profile, $txt;

	$context['profile_fields'] = array(
		'ar_pm_enabled' => array(
			'label' => $txt['ar_pm_enabled'],
			'type' => 'check',
			'input_attr' => '',
			'value' => isset($cur_profile['options']['ar_pm_enabled']) ? $cur_profile['options']['ar_pm_enabled'] : '',
		),
		'ar_pm_subject' => array(
			'label' => $txt['ar_pm_subject'],
			'subtext' => $txt['ar_pm_subject_desc'],
			'type' => 'text',
			'input_attr' => '',
			'value' => isset($cur_profile['options']['ar_pm_subject']) ? $cur_profile['options']['ar_pm_subject'] : '',
		),
		'ar_pm_body' => array(
			'type' => 'callback',
			'callback_func' => 'ar_pm_body',
		),
		'ar_pm_outbox' => array(
			'label' => $txt['ar_pm_outbox'],
			'type' => 'check',
			'input_attr' => '',
			'value' => isset($cur_profile['options']['ar_pm_outbox']) ? $cur_profile['options']['ar_pm_outbox'] : '',
		),
	);

	$context['sub_template'] = 'edit_options';
	$context['profile_header_text'] = $txt['ar_pm_profile_area'];
	$context['page_desc'] = $txt['ar_pm_profile_area'];
	$context['profile_execute_on_save'] = array('ar_pm_profile_save');
}

function template_profile_ar_pm_body()
{
	global $cur_profile, $txt;

	echo '
						<dt>
							<strong>', $txt['ar_pm_body'], '</strong>
							<br />
							<span class="smalltext">', $txt['ar_pm_body_desc'], '</span>
						</dt>
						<dd>
							<textarea id="ar_pm_body" name="ar_pm_body" cols="8" rows="40" style="width:90%; height: 300px;">', isset($cur_profile['options']['ar_pm_body']) ? $cur_profile['options']['ar_pm_body'] : '', '</textarea>
						</dd>';
}

function ar_pm_profile_save()
{
	global $context;

	$_POST['default_options'] = array(
		'ar_pm_enabled',
		'ar_pm_subject',
		'ar_pm_body',
		'ar_pm_outbox',
	);

	makeThemeChanges($context['member'], 1);
}

?>