<?php
set_time_limit(0);
ini_set("memory_limit","1024M");

class cronController extends Controller{

	public $_domains = array();
	public $_stats_domains = array();
	public $_stats_users = array();
	
	public function parselogAction(){
		$path_files_logs  = ROOT_PATH . 'log' . DS . 'fw' .DS;
		// Recuperation des fichiers
		$files = getFilesInDir($path_files_logs);
		$return = null;
		foreach($files as $k => $v){

			// Verification si fichier deja traite
			$r = $this->registry->db->count('files', array('name =' => $k));

			if($r > 0){
				goto next_boucle;
			}else{
				// Enregistement du fichier dans la base
				$file = array(
					'name'		=>	$k,
					'date_add'	=>	date('Y-m-d h:i:s'),
					'duration'	=>	'0',
					'lines'		=>	'0',
				);

				$this->registry->db->insert('files', $file);
				$r = $this->registry->db->get_one('files', array('name =' => $k));
				$file_id = $r['id'];
			}
			
			// Recuperation du contenu
			$lines = file($path_files_logs . $v);
			
			// On parcours les lignes
			$i=0;

			// Start chrono
			$startime = time();

			$data = array();

			foreach($lines as $k2 => $v2){
				$data[$i] = array();
								
				$data[$i]['file_id'] = $file_id;
				
				// Recuperation protocole
				preg_match("#proto=([A-Za-z0-9]*)#",$v2, $result);
				
				if(!empty($result)) $data[$i]['proto'] = $result[1];
				else goto next_line;
				
				// Date
				preg_match("#startime=\"([0-9]*-[0-9]*-[0-9]*\s[0-9]*:[0-9]*:[0-9]*)\"#",$v2, $result);
				if(!empty($result)){
					$tmp = explode(' ',$result[1]);
					$data[$i]['date'] = $tmp[0];
					$data[$i]['hours'] = $tmp[1];
				}				
				
				// Src name
				preg_match("#srcname=([^*\s]+)#",$v2, $result);				
				if(!empty($result)) $data[$i]['src_name'] = $result[1];

				// Ip source
				preg_match("#src=([0-9]*.[0-9]*.[0-9]*.[0-9]*)#",$v2, $result);
				if(!empty($result)) $data[$i]['src_ip'] = $result[1];

				// Src port
				preg_match("#srcport=([A-Za-z0-9]*)#",$v2, $result);				
				if(!empty($result)) $data[$i]['src_port'] = $result[1];
				
				// Src port name
				preg_match("#srcportname=([A-Za-z0-9]*)#",$v2, $result);				
				if(!empty($result)) $data[$i]['src_port_name'] = $result[1];
				
				// Dest name
				preg_match("#dstname=([^*\s]+)#",$v2, $result);				
				if(!empty($result)){
					$data[$i]['dst_name'] = str_replace('"', '',$result[1]);
				}
				
				// Dst
				preg_match("#dst=([0-9]*.[0-9]*.[0-9]*.[0-9]*)#",$v2, $result);
				if(!empty($result)) $data[$i]['dst_ip'] = $result[1];

				// Dst port
				preg_match("#dstport=([A-Za-z0-9]*)#",$v2, $result);				
				if(!empty($result)) $data[$i]['dst_port'] = $result[1];
				
				// Dst port name
				preg_match("#dstportname=([A-Za-z0-9]*)#",$v2, $result);				
				if(!empty($result)) $data[$i]['dst_port_name'] = $result[1];

				// User
				preg_match("#user=\"([A-Za-z0-9]*)\"#",$v2, $result);				
				if(!empty($result)){
					// Verification utilisateur dans la base
					$user = $this->registry->db->get_one('users', array('name =' => $result[1]));
					if(!empty($user)){
						$data[$i]['user_id'] = $user['id'];
					}else{
						$data[$i]['user_id'] = $this->registry->db->insert('users', array('name' => $result[1]));
					}					
				}else{
					$data[$i]['user_id'] = 0;
				} 
				
				// sent
				preg_match("#sent=([0-9]*)#",$v2, $result);
				if(!empty($result)) $data[$i]['sent'] = $result[1];

				// receive
				preg_match("#rcvd=([0-9]*)#",$v2, $result);
				if(!empty($result)) $data[$i]['rcvd'] = $result[1];

				// op
				preg_match("#op=([A-Za-z0-9]*)#",$v2, $result);				
				if(!empty($result)) $data[$i]['op'] = $result[1];
				
				// arg
				preg_match("#arg=\"([A-Za-z0-9]*)\"#",$v2, $result);
				if(!empty($result)) $data[$i]['arg'] = $result[1];

				
				// Internet
				$tmp = explode('.',$data[$i]['dst_ip']);
				if($tmp[0] != '10' && $tmp[0] != '172' && $tmp[0] != '192'){
					$data[$i]['internet'] = 1;
				}else{
					$data[$i]['internet'] = 0;
				}

				// Recuperation ID du domain distant
				if(isset($data[$i]['dst_name']))
					$data[$i]['dst_id'] = $this->get_domain_id($data[$i]['dst_name'], $data[$i]['dst_ip'], true);  
		

				// Generation de la stats
				$this->generate_stats($data[$i]);


				$i++;
				
				//if($i == 10000) break;
				next_line:
				if($i % 1000 == 0) echo 'Traitement en cours ... ligne '. $i .'<br/>';
			}

			// Enregistrement des logs dans la base
			$this->save_logs($data);

			// Fin du chrono
			$duration = time() - $startime;

			// Enregistrement dans la base du traitment du fichier
			$this->registry->db->update('files', array('duration' => $duration, 'lines' => $i), array('id ='=> $file_id));

			next_boucle:
		}
		
		// Save stats
		$this->save_stats();
		
		return "Traitment fini";
	}

	private function save_stats(){
		// Sauvegarde des stats domains
		foreach($this->_stats_domains as $row){
			$stats_in_db = $this->registry->db->get_one('stats_domains', array('domain_id =' => $row['domain_id'], 'date =' => $row['date']));

			if(!empty($stats_in_db)){
				// On met a jours la base
				$stats_in_db['hits'] = $stats_in_db['hits'] + $row['hits'];

				if(!empty($row['sent'])) $stats_in_db['sent'] = $stats_in_db['sent'] + $row['sent'];
				if(!empty($row['rcvd'])) $stats_in_db['rcvd'] = $stats_in_db['rcvd'] + $row['rcvd'];

				$this->registry->db->update('stats_domains', $stats_in_db);
			}else{
				// Creation nouvelle ligne
				$d_stats = array(
					'domain_id'		=> 	$row['domain_id'],
					'date'			=>	$row['date'],
					'hits'			=>	1,
					'sent'			=>	isset($row['sent']) ? $row['sent'] : 0,
					'rcvd'			=>	isset($row['rcvd']) ? $row['rcvd'] : 0,
				);

				$this->registry->db->insert_delayed('stats_domains', $d_stats);
			}

		}
	}


	/**
	 * Genere les stats utilisateurs
	 * @return [type] [description]
	 */
	private function stats_user(){
		if(!empty($data['user_id'])){
			$key_id = md5($data['user_id'].$data['date']);

			if(isset($this->_stats_users[$key_id])){
				$this->_stats_domain[$key_id]['hits'] = $this->_stats_users[$key_id]['hits'] + 1;
				if(!empty($data['sent'])) $this->_stats_users[$key_id]['sent'] = $this->_stats_users[$key_id]['sent'] + $data['sent'];
				if(!empty($data['rcvd'])) $this->_stats_users[$key_id]['rcvd'] = $this->_stats_users[$key_id]['rcvd'] + $data['rcvd'];
			}else{
				// Ajout d une nouvelle ligne
				$this->_stats_users[$key_id] = array(
					'user_id'		=> 	$user_id,
					'date'			=>	$data['date'],
					'hits'			=>	1,
					'sent'			=>	isset($data['sent']) ? $data['sent'] : 0,
					'rcvd'			=>	isset($data['rcvd']) ? $data['rcvd'] : 0,
				);
			}
		}
	}
	
	/**
	 * Genere les stats en fonctions de la ligne
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	private function generate_stats($data){
		if(!empty($data['dst_name'])){
			// generation md5 domain+date
			$key_id = md5($data['dst_name'].$data['date']);

			if(isset($this->_stats_domains[$key_id])){
				$this->_stats_domain[$key_id]['hits'] = $this->_stats_domains[$key_id]['hits'] + 1;
				if(!empty($data['sent'])) $this->_stats_domains[$key_id]['sent'] = $this->_stats_domains[$key_id]['sent'] + $data['sent'];
				if(!empty($data['rcvd'])) $this->_stats_domains[$key_id]['rcvd'] = $this->_stats_domains[$key_id]['rcvd'] + $data['rcvd'];
			}else{
				// Ajout d une nouvelle ligne
				$this->_stats_domains[$key_id] = array(
					'domain_id'		=> 	$this->get_domain_id($data['dst_name'], $data['dst_ip'], true),
					'date'			=>	$data['date'],
					'hits'			=>	1,
					'sent'			=>	isset($data['sent']) ? $data['sent'] : 0,
					'rcvd'			=>	isset($data['rcvd']) ? $data['rcvd'] : 0,
				);
			}
		}
		
	}

	private function get_domain_id($domain, $ip, $force_add = true){

		// Si premiere execution on recupere dans la base
		if(empty($this->_domains)){
			$Sql = $this->registry->db->get('domains');

			foreach($Sql as $data){
				$this->_domains[$data['name']] = $data['id'];
			}
		}
		
		if(isset($this->_domains[$domain])){
			// On a deja l ID du domain
			return $this->_domains[$domain];
		}else{
			// On definie si interne ou internet			
			$tmp = explode('.',$ip);
			if($tmp[0] != '10' && $tmp[0] != '172' && $tmp[0] != '192'){
				$internet = 1;
			}else{
				$internet = 0;
			}

			// Enregistrement du domain dans la base
			$domain_id = $this->registry->db->insert('domains', array('name' => trim($domain), 'internet' => $internet));

			// On l ajout au tableau
			$this->_domains[$domain]=$domain_id;

			return $domain_id;
		} 	
	}

	private function save_logs($data){
		foreach($data as $row){
			$this->registry->db->insert_delayed('logs', $row);
		}
	}

}
?>