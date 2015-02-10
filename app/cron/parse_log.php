<?php

set_time_limit(0);
ini_set("memory_limit","2048M");

	
$path_files_logs  = $root_dir .'log' . DS . 'fw' .DS;

// Recuperation des fichiers
$files = getFilesInDir($path_files_logs);

foreach($files as $k => $v){

	echo "\Traitement du fichier : ". $v;

	// Init var
	$GLOBALS['_domains'] = array();
	$GLOBALS['_stats_domains'] = array();

	// Verification si fichier deja traite
	$query = "SELECT COUNT(id) FROM files WHERE name = '".$k."'";
	$sql = $db->query($query);
	$r= $sql->fetchColumn();
	$sql->closeCursor();			

	if($r > 0){
		echo "\nFichier deja traite !";
		goto next_boucle;
	}else{
		// Enregistement du fichier dans la base
		echo "\n==INFO== Traitement du fichier";

		$query = "INSERT INTO files (`name`,`date_add`,`duration`,`lines`) VALUES ('".$k."','".date('Y-m-d h:i:s')."',0,0 )";
		$db->exec($query);
		$file_id = $db->LastInsertId();

		echo "\n==INFO== ID du fichier dans la table : $file_id";	
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
			$query = 'SELECT id FROM users WHERE name = "'. $result[1] .'"';
			$Sql = $db->query($query);
			$user = $Sql->fetch(PDO::FETCH_ASSOC);
			$Sql->closeCursor();

			if(!empty($user)){
				$data[$i]['user_id'] = $user['id'];
			}else{
				$query = 'INSERT INTO users (`name`) VALUE ("'.$result[1].'") ';
				$db->exec($query);
				$data[$i]['user_id'] = $db->lastInsertId();
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

		// Action
		preg_match("#action=([A-Za-z0-9]*)#",$v2, $result);				
		if(!empty($result)){
			$data[$i]['action'] = $result[1];
		}else{
			$data[$i]['action'] = 'null';
		}

		if($data[$i]['action'] == 'pass' ){
			$data[$i]['action'] = 1;
		}elseif($data[$i]['action'] == 'block'){
			$data[$i]['action']  = 2;
		}else{
			$data[$i]['action'] = 99;
		}
		
		// Internet
		$tmp = explode('.',$data[$i]['dst_ip']);
		if($tmp[0] != '10' && $tmp[0] != '172' && $tmp[0] != '192'){
			$data[$i]['internet'] = 1;
		}else{
			$data[$i]['internet'] = 0;
		}

		// Recuperation ID du domain distant
		if(isset($data[$i]['dst_name']))
			$data[$i]['dst_id'] = get_domain_id($data[$i]['dst_name'], $data[$i]['dst_ip'], true);  

		// Generation de la stats
		generate_stats($data[$i]);

		$i++;
		
		//if($i == 10000) break;
		next_line:
		if($i % 1000 == 0) echo "\n==INFO== Traitement en cours ... ligne ". $i;
	} // endforeach $lines

	// Enregistrement des logs dans la base
	echo "\n==INFO== Debut de la sauvegarde dans la base";
	save_logs($data);
	echo "\n==INFO== Sauvegarde dans la base termine";

	// Save stats
	echo "\n==INFO== Sauvegarde des stats";
	save_stats();

	// Fin du chrono
	$duration = time() - $startime;

	echo "\n==INFO== Fichier traite en : ". $duration ." secondes";

	$query = 'UPDATE files SET `duration` = "'. $duration .'" WHERE id = '. $file_id .' ';
	$db->exec($query);

	next_boucle:

	// Memory free
	unset($data);
	unset($lines);
	unset($GLOBALS['_domains']);
	unset($GLOBALS['_stats_domains']);

} // end foreach $files

echo "\n==INFO== Suppression du cache ...";
// On supprime le cache 
$files = getFilesInDir($root_dir . 'cache');
foreach($files as $k => $v){
	if(is_file($root_dir . 'cache' . DS . $k)){
		@unlink($root_dir . 'cache' . DS . $k);
	}
}

echo "\n==INFO== Generation du cache ...";
GetTopDomainReceive();	
GetTopDomainSent();
GetTopHosts();
GetTopUsers();
GetDays();
GetDaysAsc();
GetInternetInternal();
GetDataTraffic();
GetPortInternet();
GetRcvdSent();

echo "\n==INFO== Traitment fini";

/**
 * Enregistrement dans la base les lignes du fichiers
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function save_logs($data){
	global $db;

	foreach($data as $row){
		$db->exec(get_insert_delayed('logs', $row));
	}
}

/**
 * Recupere l ID du domain
 * @param  [type]  $domain    [description]
 * @param  [type]  $ip        [description]
 * @param  boolean $force_add [description]
 * @return [type]             [description]
 */
function get_domain_id($domain, $ip, $force_add = true){
	global $db;

	// Si premiere execution on recupere dans la base
	if(empty($GLOBALS['_domains'])){
		$query = "SELECT * FROM domains";
		$Sql = $db->query($query);
		$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

		foreach($result as $data){
			$GLOBALS['_domains'][$data['name']] = $data['id'];
		}
	}
	
	if(isset($GLOBALS['_domains'][$domain])){
		// On a deja l ID du domain
		return $GLOBALS['_domains'][$domain];
	}else{
		// On definie si interne ou internet			
		$tmp = explode('.',$ip);
		if($tmp[0] != '10' && $tmp[0] != '172' && $tmp[0] != '192'){
			$internet = 1;
		}else{
			$internet = 0;
		}

		$query = 'INSERT INTO domains (`name`,`internet`) VALUES ("'.trim($domain).'",'.$internet.')';
		$db->exec($query);
		$GLOBALS['_domains'][$domain] = $db->lastInsertId();
		return $GLOBALS['_domains'][$domain];
	} 	
}

function generate_stats($data){
	if(!empty($data['dst_name'])){
		// generation md5 domain+date
		$key_id = md5($data['dst_name'].$data['date']);

		if(isset($GLOBALS['_stats_domains'][$key_id])){
			$GLOBALS['_stats_domains'][$key_id]['hits'] = $GLOBALS['_stats_domains'][$key_id]['hits'] + 1;
			if(!empty($data['sent'])) $GLOBALS['_stats_domains'][$key_id]['sent'] = $GLOBALS['_stats_domains'][$key_id]['sent'] + $data['sent'];
			if(!empty($data['rcvd'])) $GLOBALS['_stats_domains'][$key_id]['rcvd'] = $GLOBALS['_stats_domains'][$key_id]['rcvd'] + $data['rcvd'];
		}else{
			// Ajout d une nouvelle ligne
			$GLOBALS['_stats_domains'][$key_id] = array(
				'domain_id'		=> 	get_domain_id($data['dst_name'], $data['dst_ip'], true),
				'date'			=>	$data['date'],
				'hits'			=>	1,
				'sent'			=>	isset($data['sent']) ? $data['sent'] : 0,
				'rcvd'			=>	isset($data['rcvd']) ? $data['rcvd'] : 0,
			);
		}
	}	
}

function save_stats(){
	global $db;

	// Sauvegarde des stats domains
	foreach($GLOBALS['_stats_domains'] as $row){

		$query = 'SELECT * FROM stats_domains WHERE domain_id = '. $row['domain_id'] .' AND `date` = "'. $row['date'] .'"';
		$sql = $db->query($query);
		$stats_in_db = $sql->fetch(PDO::FETCH_ASSOC);
		$sql->closeCursor();

		if(!empty($stats_in_db)){
			// On met a jours la base
			$stats_in_db['hits'] = $stats_in_db['hits'] + $row['hits'];

			if(!empty($row['sent'])) $stats_in_db['sent'] = $stats_in_db['sent'] + $row['sent'];
			if(!empty($row['rcvd'])) $stats_in_db['rcvd'] = $stats_in_db['rcvd'] + $row['rcvd'];

			$query = 'UPDATE stats_domains SET `hits` = "'.$stats_in_db['hits'] .'", `sent` = "' . $stats_in_db['sent']  .'",  `rcvd` = "' . $stats_in_db['rcvd']  .'" WHERE id = '.$stats_in_db['id'] .' ';
			$db->exec($query);
		}else{
			// Creation nouvelle ligne
			$d_stats = array(
				'domain_id'		=> 	$row['domain_id'],
				'date'			=>	$row['date'],
				'hits'			=>	1,
				'sent'			=>	isset($row['sent']) ? $row['sent'] : 0,
				'rcvd'			=>	isset($row['rcvd']) ? $row['rcvd'] : 0,
			);

			$db->exec(get_insert_delayed('stats_domains', $d_stats));
		}// end if
	}// end foreach
}// end function


function GetTopDomainReceive(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	// Init Block Cache
	$cache->get('IndexTopDomainReceive'.$date);

	// Requete
	$Query = 'SELECT d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent FROM stats_domains sd LEFT JOIN domains d ON sd.domain_id = d.id WHERE d.internet = 1 AND sd.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" GROUP BY sd.domain_id ORDER BY dom_rcvd DESC LIMIT 10';
	$Sql = $db->query($Query);

	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$cache->save(serialize($result));

} // end function GetTopDomainReceive

function GetTopDomainSent(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	// Init Block Cache
	$cache->get('IndexTopDomainSent'.$date);

	// Requete
	$Query = 'SELECT d.name, SUM(sd.rcvd) as dom_rcvd, SUM(sd.sent) as dom_sent FROM stats_domains sd LEFT JOIN domains d ON sd.domain_id = d.id WHERE d.internet = 1 AND sd.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" GROUP BY sd.domain_id ORDER BY dom_sent DESC LIMIT 10';
	$Sql = $db->query($Query);

	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$cache->save(serialize($result));
} // end function GetTopDomainSent

function GetTopHosts(){
	echo "\n-> GetTopHosts";
	$startime = microtime(true);

	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	// Init Block Cache
	$cache->get('IndexTopHosts'.$date);

	// Preparation et execution de la requete
	$Query = 'SELECT l.src_ip, l.src_name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent FROM logs l WHERE l.internet = 1 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" GROUP BY l.src_ip ORDER BY rcvd DESC LIMIT 10';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	// Sauvegarde en cache
	$cache->save(serialize($result));	

	$endtime = microtime(true) - $startime;
	echo " executer en ". $endtime;
} // end GetTopHost

function GetTopUsers(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	// Init Block Cache
	$cache->get('IndexTopUsers'.$date);

	$Query = 'SELECT u.name, SUM(l.rcvd) as rcvd, SUM(l.sent) as sent, l.user_id FROM logs l LEFT JOIN users u ON l.user_id = u.id WHERE l.internet = 1 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" GROUP BY l.user_id ORDER BY rcvd DESC LIMIT 10';

	$Sql = $db->query($Query);

	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$cache->save(serialize($result));
} // end function GetTopUsers

function GetDays(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]);

	$cache->get('DaysInDbForStats'.$date);

	$Query = 'SELECT DISTINCT(l.date) as day  FROM logs l WHERE l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" ORDER BY day DESC';
	$Sql = $db->query($Query);

	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$cache->save(serialize($result));
} // end function GetDays

function GetDaysAsc(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]);

	$cache->get('DaysInDbForStatsASC'.$date);

	$Query = 'SELECT DISTINCT(l.date) as day  FROM logs l WHERE l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" ORDER BY day ASC';
	$Sql = $db->query($Query);

	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$cache->save(serialize($result));
}

function GetInternetInternal(){

	echo "\n->GetInternetInternal";
	$startime = microtime(true);
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	$cache->get('GetInternetInternal'.$date);

	// Recuperation du dernier jour du mois
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	//20150205 :
	// -> Changement de la requete : recuperation des lignes et calcul via PHP
	$Query = 'SELECT rcvd, sent FROM logs l WHERE l.internet = 1 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" ';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);
	$data_internet = array('sent'=>0,'rcvd'=>0);
	
	foreach($result as $row){
		$data_internet['sent'] = $data_internet['sent'] + $row['sent'];
		$data_internet['rcvd'] = $data_internet['rcvd'] + $row['rcvd'];
	}
	
	$Query = 'SELECT rcvd, sent FROM logs l WHERE l.internet = 0 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" ';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);
	$data_internal = array('sent'=>0,'rcvd'=>0);
	
	foreach($result as $row){
		$data_internal['sent'] = $data_internal['sent'] + $row['sent'];
		$data_internal['rcvd'] = $data_internal['rcvd'] + $row['rcvd'];
	}
	
	/*-
	$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs l WHERE l.internet = 1 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" LIMIT 1';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);
	$data_internet = $result[0];
	

	$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs l WHERE l.internet = 0 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" LIMIT 1';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);
	$data_internal = $result[0];
	-*/
	
	$tot_internal = $data_internal['sent']+$data_internal['rcvd'];
	$tot_internet = $data_internet['sent']+$data_internet['rcvd'];

	$array = array();

	$array[] = array('label'=>'Internet', 'data'=>$tot_internet);
	$array[] = array('label'=>'VPN/VLAN', 'data'=>$tot_internal);

	$cache->save(serialize($array));
	
	$endtime = microtime(true) - $startime;
	echo " executer en ". $endtime;
} // end function GetInternetInternal


function GetDataTraffic(){
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	// Recuperation du dernier jour du mois 
	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]); 

	$days = unserialize($cache->get('DaysInDbForStatsASC'.$date));

	$cache->get('CumulTraffic'.$date);

	$date_global = array();
	// On boucle sur les jours pour recupere les stats
	foreach($days as $day){
		$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs WHERE date = "'. $day['day'] .'" LIMIT 1';
		$Sql = $db->query($Query);
		$Result = $Sql->fetchAll(PDO::FETCH_ASSOC);

		$tot = number_format( ($Result[0]['rcvd'] + $Result[0]['sent'])/1048576, 0, '.', '');
		$day_number = explode('-', $day['day']);
		$data_global[] = array(''.$day_number[2] .'',$tot);
	}

	$cache->save(serialize($data_global));	

	// Recuperation traffic internet
	$cache->get('CumulTrafficInternet'.$date);

	$data_internet = array();
	// On boucle sur les jours pour recupere les stats
	foreach($days as $day){
		$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs WHERE date = "'. $day['day'] .'" AND internet = 1 LIMIT 1';
		$Sql = $db->query($Query);
		$Result = $Sql->fetchAll(PDO::FETCH_ASSOC);

		$tot = number_format( ($Result[0]['rcvd'] + $Result[0]['sent'])/1048576, 0, '.', '');
		$day_number = explode('-', $day['day']);
		$data_internet[] = array(''.$day_number[2] .'',$tot);
	}
	$cache->save(serialize($data_internet));

	// Recuperation traffic interne (VPN/VLAN/Bridge)
	$cache->get('CumulTrafficInterne'.$date);
	$data_interne = array();
	// On boucle sur les jours pour recupere les stats
	foreach($days as $day){
		$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs WHERE date = "'. $day['day'] .'" AND internet = 0 LIMIT 1';
		$Sql = $db->query($Query);
		$Result = $Sql->fetchAll(PDO::FETCH_ASSOC);

		$tot = number_format( ($Result[0]['rcvd'] + $Result[0]['sent'])/1048576, 0, '.', '');
		$day_number = explode('-', $day['day']);
		$data_interne[] = array(''.$day_number[2] .'',$tot);
	}
	$cache->save(serialize($data_interne));
}

/**
 * Stats sur les ports 80 / 443 et 21 pour le mois en cours
 */
function GetPortInternet(){
	echo "\n-> GetPortInternet";
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]);

	$cache->get('TopPortInternet'.$date);
	$Query = 'SELECT dst_port, SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs l WHERE l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'" AND l.internet = 1 AND (dst_port = 80 OR dst_port = 443 OR dst_port = 21) GROUP BY l.dst_port';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);

	$data = array();

	foreach($result as $row){
		if(($row['rcvd']+$row['sent']) > 0){
			$data[] = array(
				'label'	=>	$row['dst_port'],
				'data'	=>	number_format( ($row['rcvd'] + $row['sent'])/1048576, 0, '.', ''),
			);
		}			
	}

	$cache->save(serialize($data));
}

/**
 * Stats sur envoie/reception données internet
 */
function GetRcvdSent(){
	echo "\n-> GetRcvdSent";
	global $db, $cache;

	$date = date('Y-m-d');
	$tmp = explode('-', $date);
	$date = $tmp[0].'-'.$tmp[1];

	$last_day = cal_days_in_month(CAL_GREGORIAN, $tmp[1], $tmp[0]);	

	$cache->get('GetRcvdSent'.$date);

	$Query = 'SELECT SUM(rcvd) as rcvd, SUM(sent) as sent FROM logs l WHERE l.internet = 1 AND l.date BETWEEN "'.$date.'-01" AND "'.$date.'-'.$last_day.'"';
	$Sql = $db->query($Query);
	$result = $Sql->fetchAll(PDO::FETCH_ASSOC);
	$cache->save(serialize($result[0]));
}