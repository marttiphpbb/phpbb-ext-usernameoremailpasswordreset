<?php
/**
* phpBB Extension - marttiphpbb emailonlypasswordreset
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\emailonlypasswordreset\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\event\data as event;
use phpbb\db\driver\factory as db;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\language\language;

class listener implements EventSubscriberInterface
{
	/** @var db */
	protected $db;

	/** @var request */
	protected $request;

	/** @var user */
	protected $user;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	/**
	 * @param request $request
	*/
	public function __construct(
		db $db,
		request $request,
		user $user,
		language $language
	)
	{
		$this->db = $db;
		$this->request = $request;
		$this->user = $user;
		$this->language = $language;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.index_modify_page_title' 
				=> 'core_index_modify_page_title',
			'core.ucp_remind_modify_select_sql'
				=> 'core_ucp_remind_modify_select_sql',
			'core.twig_environment_render_template_before'
				=> 'core_twig_environment_render_template_before',
		];
	}

	public function core_index_modify_page_title(event $event)
	{
		
	}

	public function core_ucp_remind_modify_select_sql(event $event)
	{
		$sql_array = $event['sql_array'];
		$email = $event['email'];
		
		$sql_array['WHERE'] = 'user_email_hash = \'';
		$sql_array['WHERE'] .= $this->db->sql_escape(phpbb_email_hash($email));
		$sql_array['WHERE'] .= '\'';

		// The user is fetched here only to provide an alternative error message
		// when not found.
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$user_row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$user_row)
		{
			$this->language->add_lang('error', 'marttiphpbb/emailonlypasswordreset');
			trigger_error('MARTTIPHPBB_EMAILONLYPASSWORDRESET_NO_EMAIL_USER');
		}		

		$event['sql_array'] = $sql_array;
	}

	public function core_twig_environment_render_template_before(event $event)
	{
		$context = $event['context'];

		error_log(json_encode($context));
	}
}
