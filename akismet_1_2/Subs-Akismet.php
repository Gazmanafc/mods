<?php
// Version: 1.0: Subs-Akismet.php

if (!defined('SMF'))
	die('Hacking attempt...');

function akismet_load_theme()
{
	global $context, $topic;

	// Is this a topic being displayed?
	if (empty($_REQUEST['action']) && !empty($board) && !empty($topic))
	{
		// Looking through the topic table can be slow, so try using the cache first.
		if (($spam = cache_get_data('akismet-spam-topic-' . $topic, 3600)) === NULL)
		{
			$request = $smcFunc['db_query']('', '
				SELECT id_topic
				FROM {db_prefix}topics
				WHERE id_topic = {int:id_topic}',
				array(
					'id_topic' => $topic,
				)
			);

			// So did it find anything?
			if ($smcFunc['db_num_rows']($request))
			{
				list ($spam) = $smcFunc['db_fetch_row']($request);
				$smcFunc['db_free_result']($request);
				// Save save save.
				cache_put_data('akismet-spam-topic-' . $topic, $spam, 120);
			}
		}

		if (!empty($span))
		{
			// So we now know this is a spam topic. Show a warning.
			loadLanguage('Akismet');
			loadTemplate('Akismet');
			$context['template_layers'][] = 'akismet_warn_topic';
		}
	}
}

function akismet_create_topic($msg_options, $topic_options, $poster_options)
{
	global $modSettings, $scripturl, $smcFunc, $sourcedir;

	require($sourcedir . '/Akismet.class.php');

	// If the subject is 'akismet-test-123', then mark it as spam (this is a test)
	if ($msg_options['subject'] == 'akismet-test-123')
		$spam = true;
	else
	{
		// If the API key has been set
		if (isset($modSettings['akismetAPIKey']) && $modSettings['akismetAPIKey'] != "")
		{
			// Set up the Akismet class
			$akismet = new Akismet($scripturl, $modSettings['akismetAPIKey']);
			$akismet->setAuthor($poster_options['name']);
			$akismet->setAuthorEmail($poster_options['email']);
			//$akismet->setCommentAuthorURL(""); -- URL's not used in SMF.
			$akismet->setContent($msg_options['body']);
			if (!empty($topic_options['id']))
				$akismet->setPermalink($scripturl . '?topic=' . $topicOptions['id']);
			$akismet->setType('smf-post');

			// Now, the moment of truth... Send the post to Akismet
			$akismet_return = $akismet->isSpam();
			// Was the server down?
			if ($akismet_return === 'conn_error')
			{
				// Assume it's not spam. We log an error to the error log later
				$spam = false;

				// Log it!
				if (empty($modSettings['akismetNoLog']))
					log_error(sprintf($txt['akismet_cant_connect2'], $_POST['guestname'], $scripturl . '?topic=' . $topic . (isset($_REQUEST['msg']) ? '.msg' . $_REQUEST['msg'] : '')));
			}
			// Is this... SPAM?!?!?!
			elseif ($akismet_return === true)
			{
				// Oh, the horror! Someone posted spam to your forum!
				$spam = true;
			}
			// All good, no spam today :-)
			else
				$spam = false;
		}
		else
			// No API key, assume it isn't spam
			$spam = false;
	}

	if ($spam)
	{
		// Mark the message as spam and unapprove the post. Post moderation is a big help here. :)
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET spam = 1,
				approved = 0,
				unapproved_posts = 1
			WHERE id_topic = {int:id_topic}',
			array(
				'id_topic' => $topic_options['id'],
			)
		);
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}messages
			SET approved = 0
			WHERE id_msg = {int:id_msg}',
			array(
				'id_msg' => $msg_options['id'],
			)
		);

		// Increase spam count
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}settings
			SET value = value + 1
			WHERE variable = {string:akismetCaughtSpam}',
			array(
				'akismetCaughtSpam' => 'akismetCaughtSpam',
			)
		);
	}
}

function akismet_admin_areas(&$admin_areas)
{
	global $txt;

	loadLanguage('Akismet');
	$admin_areas['config']['areas']['modsettings']['subsections']['akismet'] = array($txt['akismet_title']);
}

function akismet_modify_modifications(&$sub_actions)
{
	$sub_actions['akismet'] = 'ModifyAkismetSettings';
}

function ModifyAkismetSettings($return_config = false)
{
	global $context, $txt, $scripturl;

	$config_vars = array(
		array('message', 'akismetCaughtSpam'),
		'',
		array('text', 'akismetAPIKey', 20, 'subtext' => $txt['akismet_apikey_message']),
	);

	if ($return_config)
		return $config_vars;

	if (isset($_GET['save']))
	{
		checkSession();

		saveDBSettings($config_vars);
		writeLog();

		redirectexit('action=admin;area=modsettings;sa=akismet');
	}

	$context['post_url'] = $scripturl . '?action=admin;area=modsettings;save;sa=akismet';
	$context['settings_title'] = $txt['akismet_title'];

	prepareDBSettingContext($config_vars);
}