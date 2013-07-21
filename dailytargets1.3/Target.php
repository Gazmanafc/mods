<?php
function dt2()
{
	global $context, $smcFunc, $request, $new_posts;

	$new_posts = 0;
   	$time = date('F d, Y, 00:00:00');
   	$time2 = strtotime($time);
   	$request = $smcFunc['db_query']('', '
   		SELECT id_topic
   		FROM {db_prefix}messages
   		WHERE poster_time >= {int:time}',
   		array(
   			'time' => $time2,
   		)
   	);
   	$new_posts = $smcFunc['db_num_rows']($request);
   	$smcFunc['db_free_result']($request);
}
?>