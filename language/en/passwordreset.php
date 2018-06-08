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
	'MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_EXPLAIN'
		=> 'You only need to fill in the username OR the email, not both.',
]);
