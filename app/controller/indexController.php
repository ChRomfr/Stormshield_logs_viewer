<?php

class indexController extends Controller{

	public function indexAction(){	
		
		// On recupere la date si passe par l URL
		if(isset($_GET['date'])){
			$date_search = $_GET['date'];
		}else{
			$date_search = date('Y-m');
		}
		
		$tmp = explode('-', $date_search);
		$date = $tmp[0].'-'.$tmp[1];

		// Mois precedent
		if($tmp[1] == '01'){
			$prev = $tmp[0]-1 .'-12';
		}else{
			$m = $tmp[1]-1;
			$prev = $tmp[0] .'-' .$m;
		}

		// Mois suivant
		if($tmp[1] == '12'){
			$y = $tmp[0]+1;
			$next = $y .'-01';
		}else{
			$m = $tmp[1]+1;
			$next = $tmp[0] .'-' .$m;
		}

		$this->registry->smarty->assign('date', $date);
		$this->registry->smarty->assign('prev_month', $prev);
		$this->registry->smarty->assign('next_month', $next);

		// Lib JS et CSS 
		$this->registry->load_web_lib('fw/index_index.js','js','footer');
		$this->registry->load_web_lib('flot/excanvas.min.js','js', 'footer');
		$this->registry->load_web_lib('flot/jquery.flot.js','js', 'footer');
		$this->registry->load_web_lib('flot/jquery.flot.pie.js','js', 'footer');
		$this->registry->load_web_lib('flot/jquery.flot.categories.js','js', 'footer');

		
		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'index.tpl');
	}
	
	public function GetTopDomainReceiveAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

		$where_sd_date = 'sd.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

		if(!$data = $this->registry->cache->get('IndexTopDomainReceive'.$date) ){

			$data = $this->registry->db->select('d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent')
						->from('stats_domains sd')
						->left_join('domains d','sd.domain_id = d.id')
						->where('d.internet = 1')
						->where($where_sd_date)
						->group_by('sd.domain_id')
						->order('dom_rcvd DESC')
						->limit(10)
						->get();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}		

		$this->registry->smarty->assign('data', $data);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopDomainReceive.tpl');
	}

	public function GetTopDomainSentAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

		$where_sd_date = 'sd.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

		$data = $this->registry->db->select('d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent')
						->from('stats_domains sd')
						->left_join('domains d','sd.domain_id = d.id')
						->where('d.internet = 1')
						->where($where_sd_date)
						->group_by('sd.domain_id')
						->order('dom_sent DESC')
						->limit(10)
						->get();

		$this->registry->smarty->assign('data', $data);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopDomainSent.tpl');

	}

	public function GetTopUsersAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

		$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

		if(!$data = $this->registry->cache->get('IndexTopUsers'.$date) ){

		$data = $this->registry->db->select('u.name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent, l.user_id')
						->from('logs l')
						->left_join('users u','l.user_id = u.id')
						->where('l.internet = 1')
						->where($where_l_date)
						->group_by('l.user_id')
						->order('rcvd DESC')
						->limit(10)
						->get();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$this->registry->smarty->assign('data', $data);
		$this->registry->smarty->assign('date',$date);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopUsers.tpl');

	}

	public function GetTopHostsAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

		$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

		if(!$data = $this->registry->cache->get('IndexTopHosts'.$date) ){

		$data = $this->registry->db->select('l.src_ip, l.src_name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent')
						->from('logs l')
						->where('l.internet = 1')
						->where($where_l_date)
						->group_by('l.src_ip')
						->order('rcvd DESC')
						->limit(10)
						->get();
			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$this->registry->smarty->assign('data', $data);
		$this->registry->smarty->assign('date', $date);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopHosts.tpl');
	}

	/**
	 * Retourne le cumul Receive / Sent data
	 * 
	 */
	public function GetRcvdSentAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		if(!$data = $this->registry->cache->get('GetRcvdSent'.$date) ){

			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

			$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

			$data = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
					->from('logs l')
					->where('l.internet = 1')
					->where($where_l_date)
					->get_one();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$array = array();

		$array[] = array('label'=>'Receive', 'data'=>$data['rcvd']);
		$array[] = array('label'=>'Sent', 'data'=>$data['sent']);

		return json_encode($array, JSON_NUMERIC_CHECK);
	}

	/**
	 * Recupere le % de traffic Internet et Interne (VLAN, VPN ...)
	 * Retourne un JSON pour graph
	 */
	public function GetInternetInternalAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		if(!$data = $this->registry->cache->get('GetInternetInternal'.$date) ){

			// Recuperation du dernier jour du mois
			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

			$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

			$data_internet = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
								->from('logs l')
								->where('l.internet = 1')
								->where($where_l_date)
								->get_one();

			$data_internal = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
								->from('logs l')
								->where('l.internet = 0')
								->where($where_l_date)
								->get_one();
		
			$tot_internal = $data_internal['sent']+$data_internal['rcvd'];
			$tot_internet = $data_internet['sent']+$data_internet['rcvd'];

			$array = array();

			$array[] = array('label'=>'Internet', 'data'=>$tot_internet);
			$array[] = array('label'=>'VPN/VLAN', 'data'=>$tot_internal);

			$this->registry->cache->save(serialize($array));
			$data = $array;
		}else{
			$data = unserialize($data);
		}

		return json_encode($data, JSON_NUMERIC_CHECK);
	}

	/**
	 * Recupere les données sur les ports pour graph
	 */
	public function GetPortInternetAction(){
		$date = $_GET['date'];

		if(!$data = $this->registry->cache->get('TopPortInternet'.$date)){
			$tmp = explode('-', $date);
			$date = $tmp[0].'-'.$tmp[1];

			$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

			$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

			$sql = $this->registry->db->select('dst_port, SUM(rcvd) as rcvd, SUM(sent) as sent')
					->from('logs l')
					->where('l.internet = 1 AND (dst_port = 80 OR dst_port = 443 OR dst_port = 21)')
					->where($where_l_date)
					->group_by('l.dst_port')
					->get();
			
			$data = array();

			foreach($sql as $row){
				if(($row['rcvd']+$row['sent']) > 0){
					$data[] = array(
						'label'	=>	$row['dst_port'],
						'data'	=>	number_format( ($row['rcvd'] + $row['sent'])/1048576, 0, '.', ''),
					);
				}			
			}

			$this->registry->cache->save(serialize($data));	
		}else{
			$data = unserialize($data);
		}	

		return json_encode($data, JSON_NUMERIC_CHECK);
	}

	/**
	 * Retour les jours pour la liste derouante
	 */
	public function GetDaysAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 
		$this->registry->cache->setLifeTime(36000);

		if(!$data = $this->registry->cache->get('DaysInDbForStats'.$date) ){
			$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

			$data = $this->registry->db->select('DISTINCT(l.date) as day')
								->from('logs l')
								->where($where_l_date)
								->order('day DESC')
								->get();
			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		// Envoie a smarty
		$this->registry->smarty->assign('days', $data);
		$this->registry->smarty->assign('date',$_GET['date']);
		$this->registry->cache->setLifeTime(3600);
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetDays.tpl');
	}

	/**
	 * Retourne les data pour graph
	 */
	public function GetDataTrafficAction(){
		$date = $_GET['date'];
		$tmp = explode('-', $date);
		$date = $tmp[0].'-'.$tmp[1];

		// Recuperation des jours		

		// Recuperation du dernier jour du mois
		$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

		if(!$days = $this->registry->cache->get('DaysInDbForStatsASC'.$date) ){
			$where_l_date = 'l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';

			$days = $this->registry->db->select('DISTINCT(l.date) as day')
								->from('logs l')
								->where($where_l_date)
								->get();
								
			$this->registry->cache->save(serialize($days));
		}else{
			$days = unserialize($days);
		}

		$data = array();

		// Recuperation cumul traffic pour le mois en cours
		if(!$data_global = $this->registry->cache->get('CumulTraffic'.$date)){

			$date_global = array();
			// On boucle sur les jours pour recupere les stats
			foreach($days as $day){
				$sql = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')->from('logs')->where('date = "'. $day['day'] .'"')->get_one();
				$tot = number_format( ($sql['rcvd'] + $sql['sent'])/1048576, 0, '.', '');
				//$data_global[$day['day']] = $tot;
				$day_number = explode('-', $day['day']);

				$data_global[] = array(''.$day_number[2] .'',$tot);
			}
			$this->registry->cache->save(serialize($data_global));
		}else{
			$data_global = unserialize($data_global);
		}

		array_push($data, $data_global);

		// Recuperation traffic internet
		if(!$data_internet = $this->registry->cache->get('CumulTrafficInternet'.$date)){

			$data_internet = array();
			// On boucle sur les jours pour recupere les stats
			foreach($days as $day){
				$sql = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')->from('logs')->where('date = "'. $day['day'] .'" AND internet = 1')->get_one();
				$tot = number_format( ($sql['rcvd'] + $sql['sent'])/1048576, 0, '.', '');
				//$data_global[$day['day']] = $tot;
				$day_number = explode('-', $day['day']);
				$data_internet[] = array(''.$day_number[2].'',$tot);
			}
			$this->registry->cache->save(serialize($data_internet));
		}else{
			$data_internet = unserialize($data_internet);
		}

		array_push($data, $data_internet);

		// Recuperation traffic interne (VPN/VLAN/Bridge)
		if(!$data_interne = $this->registry->cache->get('CumulTrafficInterne'.$date)){

			$date_interne = array();
			// On boucle sur les jours pour recupere les stats
			foreach($days as $day){
				$sql = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')->from('logs')->where('date = "'. $day['day'] .'" AND internet = 0')->get_one();
				$tot = number_format( ($sql['rcvd'] + $sql['sent'])/1048576, 0, '.', '');
				//$data_global[$day['day']] = $tot;
				$day_number = explode('-', $day['day']);
				$data_interne[] = array(''.$day_number[2].'',$tot);
			}
			$this->registry->cache->save(serialize($data_interne));
		}else{
			$data_interne = unserialize($data_interne);
		}

		array_push($data, $data_interne);

		return json_encode($data,JSON_NUMERIC_CHECK);

	}


	/**
	 * Affiche le resume du jour
	 * Idem resume mois en index/index
	 * Appel en Ajax des stats pour rendre plus fluide l'affichage
	 * @param [type] $date [description]
	 */
	public function Resume_dayAction($date){

		
		// Jour precedent
		$prev_day = date_create($date);
		date_sub($prev_day, date_interval_create_from_date_string('1 day'));

		// Jour suivant
		$next_day = date_create($date);
		date_add($next_day, date_interval_create_from_date_string('1 day'));

		// Stats mois
		$date_array = explode('-', $date);

		$this->registry->smarty->assign('prev_day', date_format($prev_day, 'Y-m-d'));
		$this->registry->smarty->assign('next_day', date_format($next_day, 'Y-m-d'));
		$this->registry->smarty->assign('date_array', $date_array);
		$this->registry->smarty->assign('date', $date);

		// Lib JS et CSS
		$this->registry->load_web_lib('circliful/jquery.circliful.min.js','js','footer');
		$this->registry->load_web_lib('circliful/jquery.circliful.css','css');
		$this->registry->load_web_lib('raphael-min.js','js','footer');
		$this->registry->load_web_lib('morris/morris.min.js','js','footer');
		$this->registry->load_web_lib('morris/morris.css','css');
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'Resume_day.tpl');
	}

	/**
	 * Top domain receive du jour
	 */
	public function GetTopDomainReceive_dayAction(){
		$date = $_GET['date'];

		if(!$data = $this->registry->cache->get('IndexTopDomainReceive'.$date) ){

			$data = $this->registry->db->select('d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent')
						->from('stats_domains sd')
						->left_join('domains d','sd.domain_id = d.id')
						->where('d.internet = 1 AND date = "'.$date.'"')
						->group_by('sd.domain_id')
						->order('dom_rcvd DESC')
						->limit(10)
						->get();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}		

		$this->registry->smarty->assign('data', $data);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopDomainReceive.tpl');
	}

	/**
	 * Top domain sent du jour
	 */
	public function GetTopDomainSent_dayAction(){
		$date = $_GET['date'];

		if(!$data = $this->registry->cache->get('IndexTopDomainSent'.$date) ){

			$data = $this->registry->db->select('d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent')
							->from('stats_domains sd')
							->left_join('domains d','sd.domain_id = d.id')
							->where('d.internet = 1 AND sd.date = "'. $date .'" ')
							->group_by('sd.domain_id')
							->order('dom_sent DESC')
							->limit(10)
							->get();
			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$this->registry->smarty->assign('data', $data);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopDomainSent.tpl');
	}

	/**
	 * Top users du jours
	 */
	public function GetTopUsers_dayAction(){
		$date = $_GET['date'];

		if(!$data = $this->registry->cache->get('IndexTopUsers'.$date) ){

		$data = $this->registry->db->select('u.name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent, l.user_id')
						->from('logs l')
						->left_join('users u','l.user_id = u.id')
						->where('l.internet = 1 AND l.date = "'.$date.'"')
						->group_by('l.user_id')
						->order('rcvd DESC')
						->limit(10)
						->get();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$this->registry->smarty->assign('data', $data);
		$this->registry->smarty->assign('date',$date);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopUsers.tpl');
	}// end GetTopUsers_day

	/**
	 * Top host du jour
	 */
	public function GetTopHosts_dayAction(){
		$date = $_GET['date'];

		if(!$data = $this->registry->cache->get('IndexTopHosts'.$date) ){

		$data = $this->registry->db->select('l.src_ip, l.src_name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent')
						->from('logs l')
						->where('l.internet = 1 AND l.date = "'.$date.'"')
						->group_by('l.src_ip')
						->order('rcvd DESC')
						->limit(10)
						->get();

			$this->registry->cache->save(serialize($data));
		}else{
			$data = unserialize($data);
		}

		$this->registry->smarty->assign('data', $data);
		$this->registry->smarty->assign('date',$date);

		return $this->registry->smarty->fetch(VIEW_PATH . 'index' . DS . 'GetTopHosts.tpl');
	}

	/**
	 * Retourne le cumul Receive / Sent data
	 * 
	 */
	public function GetRcvdSent_dayAction(){
		$date = $_GET['date'];

		$data = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
				->from('logs l')
				->where('l.internet = 1 AND date = "'. $date .'"')
				->get_one();

		$tot = $data['rcvd'] + $data['sent'];
		$data['rcvd_percent'] = round((100*$data['rcvd'])/$tot);

		$array = array();

		$array[] = array('label'=>'Receive', 'value'=>$data['rcvd_percent']);
		$array[] = array('label'=>'Sent', 'value'=>100-$data['rcvd_percent']);

		return json_encode($array, JSON_NUMERIC_CHECK);
	}

	public function GetInternetInternal_dayAction(){
		$date = $_GET['date'];

		$data_internet = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
							->from('logs l')
							->where('l.internet = 1 AND date = "'. $date .'"')
							->get_one();

		$data_internal = $this->registry->db->select('SUM(rcvd) as rcvd, SUM(sent) as sent')
							->from('logs l')
							->where('l.internet = 0 AND date = "'. $date .'"')
							->get_one();

		$tot_rcvd = $data_internet['rcvd'] + $data_internal['rcvd'];
		$tot_sent = $data_internet['sent'] + $data_internal['sent'];

		$tot = $tot_rcvd+$tot_sent;

		$tot_internal = $data_internal['sent']+$data_internal['rcvd'];
		$tot_internet = $data_internet['sent']+$data_internet['rcvd'];

		$percent_internet = round((100*$tot_internet)/$tot);

		$array = array();

		$array[] = array('label'=>'Internet', 'value'=>$percent_internet);
		$array[] = array('label'=>'VPN/VLAN', 'value'=>100-$percent_internet);

		return json_encode($array, JSON_NUMERIC_CHECK);
	}

}// end class

function extract_domain($domain)
{
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}
