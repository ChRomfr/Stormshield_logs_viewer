<?php

class serveur extends Record{

	const Table = 'serveur_proxy';
	
	public $id;
	public $name;
	public $serveur_ip;
	public $serveur_port;
	public $serveur_user;
	public $serveur_password;
	public $log_squid;
	public $log_squidguard;
	public $table_log_squid;
	public $table_log_squidguard;
	public $dir_log_squid;
	public $dir_log_squidlog;
	public $sudo;
	
	
}