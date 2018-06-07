<?php

/**
* phpBB Extension - marttiphpbb emailonlypasswordreset
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
	'MARTTIPHPBB_EMAILONLYPASSWORDRESET_NO_EMAIL_USER'
		=> 'The email information submitted could not be found.',	
]);
