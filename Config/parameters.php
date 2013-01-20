<?php
/**
 * Paramètre le programme pour le reste de l'exécution
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace Config;

include(__DIR__.'/../He/Param.php');
include(__DIR__.'/Autoload.php');

define('ROOT_PATH', \He\Param::getRoot());

// Initialise vars
$whyUs = array();

// Masque de test pour les mails, doit impérativement être défini avant l'inclusion
// du fichier de config
define('REGEX_MAIL', '#^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$#');

// Adding appliciation specific parameters
include(ROOT_PATH.'Config/config.php');

define('SERVER_NAME', \He\Param::getServerName());

\He\Session::init();

include(ROOT_PATH.'Config/database.php');

// Create database handler
$orm = \He\ORM::getDatabase('main');

// Create table if needed
if(!$orm->tableExist(DATABASE_TABLE)) {
	\He\Trace::addTrace('La table n\'est pas présente, on doit la créer !', __FILE__, -1);
	$orm->injectSQL(ROOT_PATH.'sql/table.sql', DATABASE_TABLE);
}