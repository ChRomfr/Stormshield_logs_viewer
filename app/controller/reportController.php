<?php

class reportController extends Controller{

	public function resume_dayAction($date){
		$data = $this->registry->db->get_one('stats', array('date =' => $date));
		
		if(empty($data)){
			return "Error stats not generate";
		}
		
		$result = unserialize($data['stat']);
		
		$this->registry->smarty->assign(array(
			'date'	=>	$date,
			'top_sites'		=>	$result['top_sites'],
			'top_users'		=>	$result['top_users'],	
		));
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'resume_day.tpl');
	}
	
	public function indexAction(){
	
		$param_query = array();
	
		$date = $this->registry->Http->get('date');
		
		if( empty($date) ){
			return $this->Helper->redirect($this->Helper->getLink('index/index'), 3, 'Aucune date de selectionné');
		}
		
		$param_query['date ='] =  $date;
		
		$datas = $this->registry->db->select('ip, SUM(bytes) as cumul, count(id)')->from('access_log')->where($param_query)->group_by('ip')->order('cumul DESC')->get();
		
		// Envoie a smarty
		$this->registry->smarty->assign(array(
			'date'		=>	$date,
		));

		// Generation de la page
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'index.tpl');
		
		echo"<pre>";
		print_r($datas);
		echo"</pre>";
		
	}

	public function choosedateAction(){

		//Recuperation des dates
		$result = $this->registry->db->select('DISTINCT(date)')->from('access_log')->order('date')->get();

		$dates = array();

		$this->registry->smarty->assign('date_available', $result);

		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'choosedate.tpl');

	}

	

	public function detailAction(){
		$date = $this->registry->Http->get('date');
		$ip = $this->registry->Http->get('ip');
		$logs = array();

		$datas =	$this->registry->db 
					->select('*')
					->from('access_log')
					->where(array('date ='=> $date,  'ip =' => $ip))
					->order('time DESC')
					->get();
		

		// Parcours des logs pour formatage
		foreach($datas as $row){
			$tmp_url = parse_url($row['url']);

			if( isset($logs[$tmp_url['host']]) ){
				$logs[$tmp_url['host']]['hits']++;
				$logs[$tmp_url['host']]['bytes'] = $logs[$tmp_url['host']]['bytes'] + $row['bytes'];
			}else{
				$logs[$tmp_url['host']]['url'] = $tmp_url['host'];
				$logs[$tmp_url['host']]['hits'] = 1;
				$logs[$tmp_url['host']]['bytes'] = $row['bytes'];
			}
		}
		

		// Envoie a smarty
		$this->registry->smarty->assign(array(
			'logs'		=>	$logs,
			'date'		=>	$date,
			'ip'		=>	$ip,
		));

		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'detail.tpl');
	}
	
	public function detail_urlAction(){
		$date = $this->registry->Http->get('date');
		$url = $this->registry->Http->get('url');
		
		$datas =	$this->registry->db 
					->select('*')
					->from('access_log')
					->where(array('date ='=> $date))
					->where_free('url LIKE "%'. $url .'%"')
					->order('time DESC')
					->get();
		
		var_dump($datas);
		
	}
	
	public function ajaxsresumesitesAction($date){
	
		$result = $this->registry->db->select('SUM(al.bytes) as cumul, al.serveur_id, sp.name as site')
					->from('access_log al')
					->left_join('serveur_proxy sp','al.serveur_id = sp.id')
					->where(array('al.date =' => $date))
					->group_by('al.serveur_id')
					->get();
		
		$this->registry->smarty->assign('results', $result);
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'ajaxresumesites.tpl');
		
		var_dump($result);
	}
	
	public function ajaxtopuserdayAction($day){

		$topusers = $this->db->select('SUM(bytes) as cumul,ip')
					->from('access_log')
					->where(array('date =' => $day))
					->group_by('ip')
					->order('cumul DESC')
					->limit('10')
					->get();

		$this->registry->smarty->assign(array(
			'topusers'		=>	$topusers,
			'date'			=>	$day,
		));

		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'ajaxtopusers.tpl');
	}
	
	public function ajaxuserdayAction($day){
		$topusers = $this->db->select('SUM(bytes) as cumul,ip')
					->from('access_log')
					->where(array('date =' => $day))
					->group_by('ip')
					->order('cumul DESC')
					->get();

		$this->registry->smarty->assign(array(
			'topusers'		=>	$topusers,
			'date'			=>	$day,
		));
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'ajaxtopusers.tpl');
	}

	public function ajaxtopdomainsdayAction($day){
		$data 	=	$this->registry->db 
						->select('url, bytes')
						->from('access_log')
						->where_free('date = "'. $day .'"')
						->get();

		foreach($data as $row){
			$tmp = parse_url($row['url']);
			
			$tmp_url = array();
			
			$tmp_url['host'] = extract_domain($tmp['host']);
			
			if( isset($logs['top_sites'][$tmp_url['host']]) ){
				$logs['top_sites'][$tmp_url['host']]['hits']++;
				$logs['top_sites'][$tmp_url['host']]['bytes'] = $logs['top_sites'][$tmp_url['host']]['bytes'] + $row['bytes'];
			}else{
				$logs['top_sites'][$tmp_url['host']]['url'] = $tmp_url['host'];
				$logs['top_sites'][$tmp_url['host']]['hits'] = 1;
				$logs['top_sites'][$tmp_url['host']]['bytes'] = $row['bytes'];
			}
		}

		$bytes = array();
		$hits = array();

		foreach ($logs['top_sites'] as $key => $row) {
			$bytes[$key]  = $row['bytes'];
			$hits[$key] = $row['hits'];
		}
		
		array_multisort($bytes, SORT_DESC, $hits, SORT_ASC, $logs['top_sites']);

		$this->registry->smarty->assign('logs',$logs);
		$this->registry->smarty->assign('date',$day);

		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'ajaxtopdomainsday.tpl');
	}

	public function ajaxregeneratetopsiteAction($date){
		
		$files = getFilesInDir(ROOT_PATH.'log'.DS.'squid'.DS);
		
		// On parcour les fichiers deja existant pour les supprmés
		foreach($files as $k => $v){
			if(stripos($date,$v)){
				@unlink(ROOT_PATH.'log'.DS.'squid'.DS.$v);
			}
		}

		// On genere
		$this->generatebydayAction($date);

		return 'ok';
	}

	public function ajaxtopdownloadAction($day){

	}

	public function ajaxtophitAction($day){

	}
	
	public function generatebydayAction($date){
		set_time_limit(0);

		$path = ROOT_PATH . 'log' . DS . 'squid' . DS;

		$date_array = explode('-', $date);
		$nb_jours_in_month = date('t', mktime(12,12,12,$date_array[1],1,$date_array[0]));
		
		// On boucles sur les jours
		for ($i=1; $i <= $nb_jours_in_month; $i++) { 
			
			$logs = array();

			if(is_file($date.'-'.$i.'.shark')){
				@unlink($date.'-'.$i.'.shark');
			}

			// Recuperation des logs dans la bases
			$data 	=	$this->registry->db 
						->select('url, bytes')
						->from('access_log')
						->where_free('date = "'. $date .'-' . $i.'"')
						->get();

			// On boucles sur le resultat
			foreach($data as $row){
				$tmp = parse_url($row['url']);
				
				$tmp_url = array();
				
				$tmp_url['host'] = extract_domain($tmp['host']);
				
				if( isset($logs['top_sites'][$tmp_url['host']]) ){
					$logs['top_sites'][$tmp_url['host']]['hits']++;
					$logs['top_sites'][$tmp_url['host']]['bytes'] = $logs['top_sites'][$tmp_url['host']]['bytes'] + $row['bytes'];
				}else{
					$logs['top_sites'][$tmp_url['host']]['url'] = $tmp_url['host'];
					$logs['top_sites'][$tmp_url['host']]['hits'] = 1;
					$logs['top_sites'][$tmp_url['host']]['bytes'] = $row['bytes'];
				}
			}

			$handle = fopen($path . $date.'-'.$i.'.shark', 'w+');
			fwrite($handle, serialize($logs));
			fclose($handle);

		}// end for

		return "FIN";
	}

	public function generatemonthstatAction($date){
		set_time_limit(0);

		$logs = array();

		$path = ROOT_PATH . 'log' . DS . 'squid' . DS;

		$files = getFilesInDir($path);

		foreach($files as $k => $v){
			if(strpos($v, $date) !== false){
				// On recupere le contenu du fichier
				$handle = fopen($path . $v, 'r');
				$content = fread($handle, filesize($path . $v));
				fclose($handle);
				
				// Formatage des donnees pour leur utilisation
				$data = unserialize($content);

				// On detruit la variable pour recuperer de la memoire
				unset($content);

				// Boubles sur la tableau pour le global
				foreach($data['top_sites'] as $k => $row){
					//print('<br/><br/><br/><br/><br/><pre>');
					//print_r($row);
					$url = $row['url'];
					$hits = $row['hits'];
					$bytes = $row['bytes'];
					//exit;
					if( isset($logs['top_sites'][$url]) ){
						$logs['top_sites'][$url]['hits'] = $row['hits'] = + $logs['top_sites'][$url]['hits'];
						$logs['top_sites'][$url]['bytes'] =  $row['bytes'] = + $logs['top_sites'][$url]['bytes'];
					}else{
						$logs['top_sites'][$url]['url'] = $url;
						$logs['top_sites'][$row['url']]['hits'] = $row['hits'];
						$logs['top_sites'][$row['url']]['bytes'] = $row['bytes'];
					}

				}
				
			}
		}// endforeach
		$bytes = array();
		$hits = array();

		foreach ($logs['top_sites'] as $key => $row) {
			$bytes[$key]  = $row['bytes'];
			$hits[$key] = $row['hits'];
		}
		
		array_multisort($bytes, SORT_DESC, $hits, SORT_ASC, $logs['top_sites']);
		
	}

	public function getdataforcalendarAction(){

		set_time_limit(0);
			
		// Recuperation des jours dans la base
		$jours	=	$this->registry->db
					->select('SUM(bytes) as cumul, date')
					->from('access_log')
					//->where_free('date LIKE "'. $this->getDate() .'%"')
					->group_by('date')
					->order('date DESC')
					->get();
		
		$i=0;
		foreach($jours as $row){
			//$jours[$i]['cumul_format'] = formatBytes($row['cumul']);
			$jours[$i]['title'] = formatBytes($row['cumul']);
			$jours[$i]['start'] = $row['date'];
			$jours[$i]['url'] = $this->registry->Helper->getLink('report/index?date=') . $row['date'];
			$i++;
		}

		return json_encode($jours);

	}

	private function getDate(){
		if( is_null($this->registry->Http->get('date')) ){
			return date('Y-m-');
		}else{
			return $this->registry->Http->get('date');
		}
	}

	/**
	 * Affiche la liste des urls d un domaine
	 * @return string code html
	 */
	public function domaindetailAction(){
		
		// Envoie a smarty
		$this->registry->smarty->assign(array(
			'date'		=>	$this->registry->Http->get('date'),
			'domain'	=>	$this->registry->Http->get('url'),
		));
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'domaindetail.tpl');
	}

	public function ajaxGetDomainUrlCumulAction(){
		// Recuperation des parametres
		$domain = $this->registry->Http->get('domain');
		$date = $this->registry->Http->get('date');

		$result = $this->registry->db->select('url, bytes, SUM(bytes) as cumul, LEFT(url,30) as url_short')->from('access_log')->where_free(' url LIKE "%'. $domain .'%" AND `date` LIKE "'. $date .'%"')->group_by('url')->order('cumul DESC')->get();

		// Parcour du resultat pour avoir le cumul formate
		$i = 0;
		foreach($result as $row){
			$result[$i]['cumul_formated'] = formatBytes($row['cumul']);
			$i++;
		}

		return json_encode($result);
	}

	public function ajaxGetDomainUserCumulAction(){
		// Recuperation des parametres
		$domain = $this->registry->Http->get('domain');
		$date = $this->registry->Http->get('date');

		$result = $this->registry->db->select('ip, bytes, SUM(bytes) as cumul')
						->from('access_log')
						->where_free('url LIKE "%'. $domain .'%" AND `date` LIKE "'. $date .'%"')
						->group_by('ip')
						->order('cumul DESC')
						->get();

		// Parcour du resultat pour avoir le cumul formate
		$i = 0;
		foreach($result as $row){
			$result[$i]['cumul_formated'] = formatBytes($row['cumul']);
			$i++;
		}

		return json_encode($result);
	}


} //end class

function extract_domain($domain)
{
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}