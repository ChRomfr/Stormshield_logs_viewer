<?php

class statsController extends Controller{
	
	public function indexAction(){

		if( isset($_GET['u']) ) $user_id = $_GET['u'];
		if(isset($_GET['date']) ) $date = $_GET['date'];

		$this->registry->smarty->assign('date_view', $date);
		$this->registry->smarty->assign('user_id', $user_id);

		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'index.tpl');

	}

	/**
	 * Affiche tout les logs d'une journée
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	public function full_logs_dayAction($date){

		$this->registry->smarty->assign('date', $date);

		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'full_logs_day.tpl');
	}

	public function GetFullLogsDayAction($date){
		$where = 'date = "'. $date .'" ';

		if(isset($_GET['i']) && $_GET['i'] == 1){
			$where .= " AND l.internet = 1";
		}

		if(isset($_GET['src_ip']) && !empty($_GET['src_ip'])){
			$where .= ' AND l.src_ip = "'. $_GET['src_ip'] .'" '; 
		}

		if(isset($_GET['h_start']) && !empty($_GET['h_start'])){
			$where .= ' AND l.hours >= "'. $_GET['h_start'] .'" '; 
		}

		if(isset($_GET['ignore_dns'])){
			$where .= ' AND l.dst_port != "53"';
		}
		
		$sql = $this->registry->db->select('COUNT(id) AS nblogs')
					->from('logs l')
					->where($where)
					->get_one();

		$count_logs = $sql['nblogs'];		

		// Recuperation des logs avec paginations
		$base_url = $_SERVER['REQUEST_URI'];		
		$base_url = str_replace('GetFullLogsDay','full_logs_day', $base_url);	
		
		$Pagination = new Zebra_Pagination();
		$Pagination->base_url($base_url);
		$Pagination->records($count_logs);
		$Pagination->records_per_page(50);
		$this->registry->smarty->assign('Pagination',$Pagination);

		$logs = $this->registry->db->select('l.*, u.name as user')
					->from('logs l')
					->left_join('users u','l.user_id = u.id')
					->where($where)
					->order('l.hours')
					->limit(50)
					->offset(getOffset(50))
					->get();

		$this->registry->smarty->assign('logs', $logs);
		$this->registry->smarty->assign('nblogs', $count_logs);

		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'GetFullLogsDay.tpl');
	}

	/**
	 * Affiche les statistique pour un utilisateurs
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function userAction($uid){
		// Recuperation de la date
		$date = $_GET['date'];

		// Recuperation du l utilisateur
		$user = $this->registry->db->get_one('users', array('id =' => $uid));

		$this->registry->smarty->assign('user', $user);
		$this->registry->smarty->assign('date', $date);		

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'user.tpl');
	}

	/**
	 * Stats host (IP)
	 * @param  [type] $host [description]
	 * @return [type]       [description]
	 */
	public function hostAction($host){
		// Recuperation de la date
		$date = $_GET['date'];

		$this->registry->smarty->assign('host', $host);
		$this->registry->smarty->assign('date', $date);		

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'host.tpl');
	}

	/**
	 * GetTopDomainsHost
	 * Retourne les destinations d un host
	 */
	public function GetTopDomainsHostAction(){
		if(isset($_GET['h']) )		$host = $_GET['h'];
		if(isset($_GET['date']) )	$date = $_GET['date'];

		// Traitement de la date
		$tmp = explode('-', $date);

		// Construction de la requete
		$this->registry->db->select('d.name as domain, SUM(rcvd) as rcvd, SUM(sent) as sent')
		->from('logs l')
		->left_join('domains d','l.dst_id = d.id')
		->where('l.src_ip = "'. $host .'"');

		if(count($tmp) == 3){
			// Date complete
			$this->registry->db->where('l.date ="'.$date.'" ');
		}else{
			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 
			// Stats sur Mois
			$this->registry->db->where('l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"');
		}

		// Execution de la requete
		$data = $this->registry->db->group_by('l.dst_id')
			->order('rcvd DESC')
			->limit(30)
			->get();

		// Envoie a smarty
		$this->registry->smarty->assign('data', $data);

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'TopDomains.tpl');
	}

	/**
	 * Recupere les stats d'utilisation sur les Ports
	 */
	public function GetTopPortHostAction(){
		if(isset($_GET['h']) )		$host = $_GET['h'];
		if(isset($_GET['date']) )	$date = $_GET['date'];

		// Traitement de la date
		$tmp = explode('-', $date);

		$this->registry->db->select('l.dst_port, l.dst_port_name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent')
			->from('logs l')
			->where('l.src_ip = "'. $host .'"');

		if(count($tmp) == 3){
			// Date complete
			$this->registry->db->where('l.date ="'.$date.'" ');
		}else{
			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 
			// Stats sur Mois
			$this->registry->db->where('l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"');
		}

		// Execution de la requete
		$data = $this->registry->db->group_by('l.dst_port')
			->order('rcvd DESC')
			->limit(30)
			->get();

		// Envoie a smarty
		$this->registry->smarty->assign('data', $data);

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'TopPort.tpl');
	}

	public function GetTopDomainsAction(){

		if(isset($_GET['u']) )		$user_id = $_GET['u'];
		if(isset($_GET['date']) )	$date = $_GET['date'];

		// Traitement de la date
		$tmp = explode('-', $date);

		// Construction de la requete
		$this->registry->db->select('d.name as domain, SUM(rcvd) as rcvd, SUM(sent) as sent')
		->from('logs l')
		->left_join('users u', 'l.user_id = u.id')
		->left_join('domains d','l.dst_id = d.id')
		->where('l.user_id = '. $user_id .' AND l.internet = 1');

		if(count($tmp) == 3){
			// Date complete
			$this->registry->db->where('l.date ="'.$date.'" ');
		}else{
			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 
			// Stats sur Mois
			$this->registry->db->where('l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"');
		}

		// Execution de la requete
		$data = $this->registry->db->group_by('l.dst_id')
			->order('rcvd DESC')
			->limit(30)
			->get();

		// Envoie a smarty
		$this->registry->smarty->assign('data', $data);

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'TopDomains.tpl');
	}

	public function GetTopPort_userAction(){
		if(isset($_GET['u']) )		$user_id = $_GET['u'];
		if(isset($_GET['date']) )	$date = $_GET['date'];

		// Traitement de la date
		$tmp = explode('-', $date);

		$this->registry->db->select('l.dst_port, l.dst_port_name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent')
			->from('logs l')
			->left_join('users u', 'l.user_id = u.id')
			->where('l.user_id = '. $user_id .' AND l.internet = 1');

		if(count($tmp) == 3){
			// Date complete
			$this->registry->db->where('l.date ="'.$date.'" ');
		}else{
			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 
			// Stats sur Mois
			$this->registry->db->where('l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"');
		}

		// Execution de la requete
		$data = $this->registry->db->group_by('l.dst_port')
			->order('rcvd DESC')
			->limit(30)
			->get();

		// Envoie a smarty
		$this->registry->smarty->assign('data', $data);

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'TopPort_user.tpl');
	}

	public function GetTopPortAction(){}

	public function users_full_traffic($uid){
		$date = $_GET['date'];
	}

	
}