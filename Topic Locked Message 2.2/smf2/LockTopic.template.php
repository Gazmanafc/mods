<?php
// Version: 1.1; LockTopic

// Show an interface for setting the reason for lock
function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	//It looks like move topic. :P
	echo '
		<center><div class="content" style="width: 400px;">
			<form action="', $scripturl, '?action=lock2;topic=', $context['current_topic'], '.0" method="post" accept-charset="', $context['character_set'], '" onsubmit="submitonce(this);">
		<h3 class="catbg" style="text-align: left;"><span class="left"></span>
			', !empty($context['locked']) ? $txt['set_lock'] : $txt['set_unlock'], '
		</h3>
		<div class="windowbg2">
			<span class="topslice"><span></span></span>
';
			
	//Are we locking or unlocking?
	if(!empty($context['locked'])) {
		// Disable the reason textarea when the lock reason is unchecked...
		echo '
						<label for="postLock"><input type="checkbox" name="postLock" id="postLock" checked="checked" onclick="document.getElementById(\'reasonArea\').style.display = this.checked ? \'block\' : \'none\';" class="check" />', $txt['topicLockReasonLabel'], '</label><br />
						<div id="reasonArea" style="margin-top: 1ex;">
							', $txt['topicLockReasonDesc'], '
							<br />
						<textarea name="reason" rows="3" cols="40">', $txt['topicLocked'], '</textarea><br />
					</div>';
	}
	else {
		//Do you want to delete the "Locked" message?
		echo '
						<label for="postLockerease"><input type="checkbox" name="postLockerease" id="postLockerease" checked="checked" class="check" />', $txt['topicUnlockDeleteLabel'], '</label><br />';
	}

	//Send it!
	echo '

					<br />
					<input type="submit" value="', !empty($context['locked']) ? $txt['set_lock'] : $txt['set_unlock'], '" onclick="return submitThisOnce(this);" accesskey="s" />
			<span class="botslice"><span></span></span>
		</div>';

	//Sissst, be quite. Need some hidden data. :)
	echo '
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		<input type="hidden" name="start" value="', $_REQUEST['start'], '" />
	</form></div></center>';
}

?>