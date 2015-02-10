<?php
ini_set('display_errors','On');
$root_dir = str_replace('cmd.php', '', __FILE__);
echo $root_dir;

echo "\n=== START PHP via Console ==";
define('IN_VA',1);
define('DS', DIRECTORY_SEPARATOR); 

require 'config'.DS.'config.php';

// Connxion a la base
$db = connexion_db($DB_Configuration);

//== LIB CACHE LITE ==//
// Appel du fichier
require $root_dir . 'kernel' . DS . 'lib' . DS . 'PEAR' . DS . 'Lite.php'; 

// Generation Instance
$cache = new Cache_Lite(array('cacheDir' =>	$root_dir . 'cache'. DS, 'lifeTime' => 10800));

$argv = $_SERVER['argv'][1];

echo "\nParams : ";
print_r($argv);
require 'app'.DS.'cron'.DS.$argv.'.php'; 


echo "\n== END ===";



function connexion_db($DB_Configuration){
	$dsn = 'mysql:host=' . $DB_Configuration['serveur'] .';port='. $DB_Configuration['port']  .'; dbname='. $DB_Configuration['base'];
		
	try{	
		$db = new pdo($dsn, $DB_Configuration['utilisateur'], $DB_Configuration['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));		
	}

	catch (Exception $e){
		echo '<div><p>Erreur de connexion à la base de données</p><p>'.$e.'</div>';
		exit;
	}

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

	return $db;
}

function get_insert_delayed($table, $data){
	
	$champs = '';
	$valeurs = '';
	
	foreach($data as $k => $v){
		if($champs == '') $champs = ' `'. $k .'` '; else $champs .= ' , `'. $k .'` ';
		if($valeurs == '') $valeurs = ' "'. $v .'" '; else $valeurs .= ' , "'. $v .'" ';
	}
	
	$q = 'INSERT DELAYED INTO  '. $table .' (' . $champs . ' ) VALUES ( ' . $valeurs . ' ) ';
	
	return $q;
}

/*--- END DB FUNCTION ---*/

function getFilesInDir($dirname, $path_return = ''){
    
    // On verifie que le dossier existe
    if( !is_dir($dirname) ){
        return false;
    }
    // On ouvre le dossier
    $dir = opendir($dirname);
    // Init du tableau
    $files = array();
    // On liste les fichier
    while($file = readdir($dir)){
        if($file != '.' && $file != '..' && !is_dir($dirname.$file) && $file != ' '){
            $files[$file] = $path_return . $file;
        }
    }
    // On ferme le dossier
    closedir($dir);
    // On retourne les fichiers
    return $files;
}

?>