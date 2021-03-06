<?php

/**
* phpBB Extension - marttiphpbb usernameoremailpasswordreset
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_NO_EMAIL_ERROR'
		=> 'The email %1$s submitted could not be found.',
	'MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_DUPLICATE_EMAIL_ERROR'
		=> 'The email %1$s submitted can not be used because 
		it is present multiple times in the database.
		Use the username instead.',	
	'MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_NO_USERNAME_ERROR'
		=> 'The username %1$s submitted could not be found.',
]);
