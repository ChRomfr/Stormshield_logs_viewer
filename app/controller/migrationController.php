<?php

set_time_limit(0);
ini_set("memory_limit","1024M");

class migrationController extends Controller{
	
	public function gotoaccesslogAction(){
		
		// Recuperation des serveurs
		$servers = $this->registry->db->get('serveur_proxy');
		
		foreach($servers as $server){
			
			// recuperation du nombre d enregistrement du serveur
			$nblines = $this->registry->db->count($server['table_log_squid']);
			
			$i=0;
			$cpt = 0;
			
			while($cpt < $nblines){
				
				$offset = 1000 * $i;
				
				$logs = $this->registry->db->select('*')->from("".$server['table_log_squid']."")->limit(1000)->offset($offset)->get();
				
				$cpt = $cpt + (count($logs));
				
				foreach($logs as $log){
					unset($log['id']);
					$log['serveur_id'] = $server['id'];
					$this->registry->db->insert('access_log',$log);
				}
				
				$i++;
			}
	
		}
		
		return "Traitement terminé";
	}
	
}