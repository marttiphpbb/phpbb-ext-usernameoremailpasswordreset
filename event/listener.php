<?php
/**
* phpBB Extension - marttiphpbb usernameoremailpasswordreset
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\usernameoremailpasswordreset\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\event\data as event;
use phpbb\db\driver\factory as db;
use phpbb\template\template;
use phpbb\language\language;

class listener implements EventSubscriberInterface
{
	/** @var db */
	protected $db;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	public function __construct(
		db $db,
		language $language,
		template $template,
		string $phpbb_root_path,
		string $php_ext
	)
	{
		$this->db = $db;
		$this->language = $language;
		$this->template = $template;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.ucp_remind_modify_select_sql'
				=> 'core_ucp_remind_modify_select_sql',
			'core.page_footer_after'
				=> 'core_page_footer_after',
		];
	}

	public function core_page_footer_after(event $event)
	{
		$tpl_vars = $this->template->retrieve_vars([
			'S_IN_UCP', 
			'S_PROFILE_ACTION',
		]);

		if (!isset($tpl_vars['S_IN_UCP']) || !$tpl_vars['S_IN_UCP'])
		{
			return;
		}

		if (strpos($tpl_vars['S_PROFILE_ACTION'], 'mode=sendpassword') === false)
		{
			return;
		}

		$this->language->add_lang('passwordreset', 'marttiphpbb/usernameoremailpasswordreset');
		$this->template->assign_var('MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET', true);
	}

	public function core_ucp_remind_modify_select_sql(event $event)
	{
		$sql_array = $event['sql_array'];
		$email = $event['email'];
		$username = $event['username'];

		if ($email)
		{
			$sql_array = $this->sql_by_email($email, $sql_array);
		}
		else if ($username)
		{
			$sql_array = $this->sql_by_username($username, $sql_array);
		}

		$event['sql_array'] = $sql_array;
	}

	private function sql_by_email(string $email, array $sql_array):array
	{		
		$sql_array['WHERE'] = 'user_email_hash = \'';
		$sql_array['WHERE'] .= $this->db->sql_escape(phpbb_email_hash($email));
		$sql_array['WHERE'] .= '\'';

		$count = 0;

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		
		while($this->db->sql_fetchrow($result))
		{
			$count++;
		}
	
		$this->db->sql_freeresult($result);

		if ($count === 0)
		{
			$this->language->add_lang('error', 'marttiphpbb/usernameoremailpasswordreset');
			$err = $this->language->lang('MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_NO_EMAIL_ERROR');
			$err = vsprintf($err, [
				$email,
			]);	

			trigger_error($err);
		}
		
		if ($count > 1)
		{
			$this->language->add_lang('error', 'marttiphpbb/usernameoremailpasswordreset');
			$err = $this->language->lang('MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_DUPLICATE_EMAIL_ERROR');
			$err = vsprintf($err, [
				$email,
			]);	
			trigger_error($err);	
		}

		return $sql_array;
	}

	private function sql_by_username(string $username, array $sql_array):array
	{		
		$sql_array['WHERE'] = 'username_clean = \'';
		$sql_array['WHERE'] .= $this->db->sql_escape(utf8_clean_string($username));
		$sql_array['WHERE'] .= '\'';

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);		
		$user_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$user_data)
		{
			$this->language->add_lang('error', 'marttiphpbb/usernameoremailpasswordreset');
			$err = $this->language->lang('MARTTIPHPBB_USERNAMEOREMAILPASSWORDRESET_NO_USERNAME_ERROR');
			$err = vsprintf($err, [
				$username,
			]);	

			trigger_error($err);
		}

		return $sql_array;
	}
}
