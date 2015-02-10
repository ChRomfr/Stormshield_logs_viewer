<?php

class admController extends Controller{
	
	public function serveur_listeAction(){
		
		$servers = $this->registry->db->get('serveur_proxy');
		
		$this->registry->smarty->assign('servers',$servers);
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'adm'  . DS . 'serveur_liste.shark');
		
	}

	public function serveur_addAction(){

		if(!is_null($this->registry->Http->post('serveur'))){
			$server = new serveur($this->registry->Http->post('serveur'));
			$server->save();

			return $this->registry->Helper->redirect($this->registry->Helper->getLink("adm/serveur_liste"),3,'Serveur sauvegardé');
		}
		
		printform:
				
		return $this->registry->smarty->fetch(VIEW_PATH . 'adm'  . DS . 'serveur_add.shark');
	}
	
	/**
	 * Affiche et traite le formulaire d edition
	 * @param  int $sid Id du serveur dans la base
	 * @return string Code HTML
	 */
	public function serveur_editAction($sid){
		
		if(!is_null($this->registry->Http->post('serveur'))){
			$server = new serveur($this->registry->Http->post('serveur'));
			$server->save();

			return $this->registry->Helper->redirect($this->registry->Helper->getLink("adm/serveur_liste"),3,'Modification sauvegardée');
		}
		
		printform:
		$server = new serveur();
		$server->get($sid);
		
		$this->registry->smarty->assign('server',$server);
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'adm'  . DS . 'serveur_edit.shark');
	}

	/**
	 * Traite la supprission d un serveur de la base
	 * @param  int $sid ID du serveur dans la base
	 * @return string Code HTML
	 */
	public function serveur_deleteAction($sid){
		$server = new serveur();
		$server->delete($sid);

		$this->registry->smarty->assign('FlashMessage','Serveur supprimé');

		return $this->serveur_listeAction();
	}

	public function maintenanceAction(){
		return $this->registry->smarty->fetch(VIEW_PATH . 'report' . DS . 'generation.tpl');
	}
	
	public function cleanAction(){
		
		if(isset($_POST['date'])){
			// Format : YYYY-MM
		}
		
		return $this->registry->smarty->fetch(VIEW_PATH . 'adm' . DS . 'clean.tpl');
	}
	
	public function move_tableAction(){
		set_time_limit(0);
		ini_set("memory_limit","1024M");
		
		if(!isset($_GET['boucle'])){
			$boucle = 0;
		}else{
			$boucle = $_GET['boucle'];
			$boucle = $boucle+1;
		}

		$table_destination = 'access_log_2013';
		$date_select_start = '2013-12-01';
		$date_select_end = '2013-12-31';
		
		$data = $this->registry->db->select('*')->from('access_log')->where('date BETWEEN "'. $date_select_start .'" AND "'. $date_select_end .'" ')->limit(1000)->get();
		
		foreach($data as $row){
			$this->registry->db->insert($table_destination, $row);
			$this->registry->db->delete('access_log', $row['id']);
		}
		
		if(count($data) == 1000){
			return '<script type="text/javascript">window.location.href="http://sbou0wb0/squidphpreport/index.php/adm/move_table?boucle='.$boucle.'"</script>';
		}else{
			return 'transfert termine';
		}
		
		return print_r($data, true);
	}
}