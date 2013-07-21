<?php

/**********************************************************************************
* AdminNotes.php                                                       		      *
***********************************************************************************
* Admin Notes Mod for Simple Machines Forum                                       *
*=================================================================================*
* Mod Version:					2.0                                            	  *
* SMF Version created in:		SMF 2.0.X  			          	                  * 
* Original Mod by:				Robbo (robert@eninja.com.au)                      *
* Mod by:						Runic (runic@simplemachines.org)                  *
* Copyright 2009 by:			eNinja PTY LTD (http://www.eninja.com.au)         *
* Copyright 2012 by:			BryanDeakin.com (http://www.bryandeakin.com)      *
* Support, News, Updates at:  	http://www.simplemachines.org                     *
***********************************************************************************
* This SMF mod is free software; you may redistribute it and/or modify it under   *
* as you see fit as long as the above copyright stays intact on all pages it is   *
* currently on.                                                                   *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

/*
 	This file holds all the code used to use admin notes on your forum.
     
    void adminNotes(void)
        - validates all user data and then calls the appropriate function
        - loads the admin notes to be used in a template
     
    array loadAdminNotes(int start)
        - will use the cache
        - will show last x notes (set in this function)
        - start is for database LIMIT
        - allows certain bbc in the notes content
        - returns an array of admin notes
        
    void addAdminNote(string note)
        - assumes note is validated
        - adds a note into the database
        
    void deleteAdminNote(int id)
        - assumes everything is valid
        - deletes a not from the database
        
    int countAdminNotes(void)
        - just returns the amount of admin notes there are for paginator
        
*/

// Main function to be called from outside this file
function adminNotes()
{
    global $context, $smcFunc, $scripturl, $forum_version;
    
    // How many notes to display per page
    $limit = 10;
    
    // Adding a note
    if (isset($_POST['add_note']))
    {
        // Validate the users session
        checkSession();
        
        // If the note we are to add isn't empty then add the note and redirect
        if (!empty($_POST['admin_note']))
        {
            addAdminNote($smcFunc['htmlspecialchars'](trim($_POST['admin_note'])));
            redirectexit('action=admin');
        }
    }
    
    // Removing a note
    if (isset($_GET['sa']) && $_GET['sa'] == 'notes' && !empty($_GET['delete']))
    {
        // Validate the session
        checkSession('get');
        
        // Remove the note and redirect
        removeAdminNote($_GET['delete']);
        redirectexit('action=admin');
    }
    
    // Create paginator
    $context['paginator'] = constructPageIndex($scripturl . '?action=admin;area=index', $_GET['start'], countAdminNotes(), $limit);

    // Load all the notes
    $context['admin_notes'] = loadAdminNotes(isset($_GET['start']) ? $_GET['start'] : 0, $limit);

    // Change the styles a bit
    $context['html_headers'] .= '
            <style type="text/css">
                #live_news, #supportVersionsTable
                {
					width: 65%;
                    float: left;
                }
                #supportVersionsTable
                {
                    margin-top: -200px;
                }
                #admin_notes
                {
                    width: 34%;
                    float: left;
					margin-left: 10px;
                }
                #admin_notes_inner
                {
                    overflow: auto;
                    height: 234px;
                }
                .note_row
                {
                    padding-top: 5px;
                    padding-bottom: 5px;
                    border-bottom: 1px dashed #CCCCCC;
                }
                #notes_form
                {
                    text-align: center;
                    margin-bottom: 25px;
                }
                #notes_form textarea
                {
                    width: 90%;
                }
                #notes_form #add_note_button
                {
                    margin-top: 5px;
                    margin-right: 4px;
                    float: right;
                }
                #notes_form #notes_paginator
                {
                    float: left;
                    margin-top: 8px;
                    margin-left: 7px;
                }
            </style>';
}

// Load admin notes 
function loadAdminNotes($start, $limit)
{
    global $scripturl, $smcFunc, $context;
    
    // Simple validation
    $start = (int) $start;
    $limit = (int) $limit;
    if (empty($limit))
        $limit = 5;
    
    // BBCode alloud in a note
    $bbcode = array(
        'b', 'i', 'url', 'br',
        'u', 'color',
        'font', 'me', 's', 
    );
    
    // Load the notes from cache or query them. Only first page of notes is cached
    if (!empty($start) || ($notes = cache_get_data('admin_notes', 240)) === null)
    {
        // Query the notes
        $request = $smcFunc['db_query']('', '
            SELECT IFNULL(mem.id_member, 0) AS id_member, IFNULL(mem.real_name, lc.member_name) AS member_name,
                lc.log_time, lc.body, lc.id_comment
            FROM {db_prefix}log_comments AS lc
                LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = lc.id_member)
            WHERE lc.comment_type = {string:note}
            ORDER BY id_comment DESC
            LIMIT {int:start}, {int:limit}',
            array(
                'note' => 'a_note',
                'start' => $start,
                'limit' => $limit,
            )
        );
        
        // Now add everything to an array
        $notes = array();
        while ($note = $smcFunc['db_fetch_assoc']($request))
            $notes[] = $note;
        
        $smcFunc['db_free_result']($request);
        
        // If we jsut queried the first page then cache it
        if (empty($start))
            cache_put_data('admin_notes', $notes, 240);
    }
    
    // Now format the array into something easier to use
    $ret = array();
    foreach ($notes as $note)
        $ret[] = array(
            'id' => $note['id_comment'],
            'poster' => array(
                'id' => $note['id_member'],
                'name' => $note['member_name'],
                'link' => $note['id_member'] ? ('<a href="' . $scripturl . '?action=profile;u=' . $note['id_member'] . '" title="' . strip_tags(timeformat($note['log_time'])) . '">' . $note['member_name'] . '</a>') : $note['member_name'],
            ),
            'time' => timeformat($note['log_time']),
            'note' => parse_bbc($note['body'], true, '', $bbcode),
            'delete_href' => $scripturl . '?action=admin;area=index;sa=notes;delete=' . $note['id_comment'] . ';' . $context['session_var'] . '=' . $context['session_id'],
        );

    // Return because we are done loading :)
    return $ret;
}

// Add an admin note. Assumes everything has been validated
function addAdminNote($note)
{
    global $user_info, $smcFunc;
    
    // Only validation we use is to make sure we have something
    if (empty($note))
        return;
    
    // Insert the note into the database
    $smcFunc['db_insert']('',
        '{db_prefix}log_comments',
        array(
            'id_member' => 'int', 'member_name' => 'string', 'comment_type' => 'string', 'recipient_name' => 'string',
			'body' => 'string', 'log_time' => 'int',
        ),
        array(
            $user_info['id'], $user_info['name'], 'a_note', '', $note, time(),
        ),
        array('id_comment')
    );

    // Clear the cache and we are done
    cache_put_data('admin_notes', null, 240);
	cache_put_data('admin_notes_total', null, 240);
}

// Remove an admin note. Once again assumes everything is valid
function removeAdminNote($id)
{
    global $smcFunc;
    
    // Some simple validation
    $id = (int) $id;
    if (empty($id))
        return;
    
    // Now run the query to remove the note
    $smcFunc['db_query']('', '
        DELETE FROM {db_prefix}log_comments
        WHERE id_comment = {int:id} AND comment_type = {string:type}',
        array(
            'id' => $id,
            'type' => 'a_note',
        )
    );
    
    // And finally clear the cache
    cache_put_data('admin_notes', null, 240);
	cache_put_data('admin_notes_total', null, 240);
}

// This function simply counts how many notes there are for the paginator
function countAdminNotes()
{
    global $smcFunc;
    
    // Get the number from the cache or a query
    if (($total = cache_get_data('admin_notes_total', 240)) === null)
    {
        // Countings time
        $request = $smcFunc['db_query']('', '
            SELECT COUNT(*)
            FROM {db_prefix}log_comments
            WHERE comment_type = {string:type}',
            array(
                'type' => 'a_note',
            )
        );
        
        list ($total) = $smcFunc['db_fetch_row']($request);
        $smcFunc['db_free_result']($request);
        
        // Cacheness
        cache_put_data('admin_notes_total', $total, 240);
    }
    
    // Return the number of notes
    return $total;
}

?>