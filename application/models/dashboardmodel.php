<?php
Class Dashboardmodel extends CI_Model {
	function get_acct_reg_by_date() {
		$dates = array();
		for ($i=0; $i < 7; $i++) {
			$dates[] = date("Y-m-d", strtotime('-'. $i .' days'));
		}
		$data = array();
		foreach ($dates as $k) {
			$this->db_ragnarok->select('userid');
			$this->db_ragnarok->where('createdate',$k);
			$q = $this->db_ragnarok->get('login');
			$data += array(
				$k => $q->num_rows(),
			);
		};
		return $data;
	}
	
	function get_server_stats() {
		// Get total amount of accounts
		$this->db_ragnarok->select('account_id');
		$this->db_ragnarok->from('login');
		$this->db_ragnarok->where('sex !=', 'S');
		$q = $this->db_ragnarok->get();
		// Get total amount of characters
		$q2 = $this->db_ragnarok->count_all('char');
		//Get total amount of guilds
		$q3 = $this->db_ragnarok->count_all('guild');
		// Get total amount of characters in a guild
		$this->db_ragnarok->select('char_id');
		$this->db_ragnarok->from('char');
		$this->db_ragnarok->where('guild_id IS NOT NULL');
		$q4 = $this->db_ragnarok->get();		
		// Get total amount of zeny on all characters
		$this->db_ragnarok->select_sum('zeny');
		$q5 = $this->db_ragnarok->get('char');
		$zeny = $q5->row();
		// Get server uptime
		$this->db_ragnarok->select('starttime');
		$this->db_ragnarok->order_by('id','desc');
		$this->db_ragnarok->limit(1);
		$this->db_ragnarok->from('hat_sstatus');
		$q6 = $this->db_ragnarok->get();
		$laststartdate = $q6->row();
		$serverstart = new DateTime($laststartdate->starttime);
		$now = date('Y-m-d H:i:s');
		$sinceStart = $serverstart->diff(new DateTime($now));
		$sinceStartf = $sinceStart->d."d&nbsp;".$sinceStart->h."h&nbsp;".$sinceStart->m."m&nbsp;".$sinceStart->s."s&nbsp;";
		
		$data = array(
			'Server Uptime'				=> $sinceStartf,
			'Accounts Registered'		=> $q->num_rows(),
			'Characters Created'			=> $q2,
			'Guilds Established'			=> $q3,
			'Characters in guilds'		=> $q4->num_rows(),
			'Zeny in Circulation'		=> $zeny->zeny,
		);
		return $data;
	}
}