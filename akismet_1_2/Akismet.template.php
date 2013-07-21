<?php

function template_akismet_warn_topic_below() {}
function template_akismet_warn_topic_above()
{
	global $txt;

	echo '
	<div id="profile_error">
		', $txt['akismet_spam_warning'], '
	</div>';
}

?>