<?php
/**
 * Class de chargement automatique des class de l'application.
 * Celle-ci doivent respecter les namespace correspondant à leur chemin
 * 
 * Exemple : 
 * Pour charger la class He\ORM le fichier ORM.php doit être dans le répertoire
 * He.
 * Pour charger la class He\ORM\DataBase le fichier DataBase.php doit être dans
 * le répertoire He\ORM.
 * 
 * Note :
 * Les class doivent avoir la même case que le nom de fichier. 
 * De même pour les répertoire du namespace.
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace Config;

class Autoload
{
	/**
	 * Chargement des class en respectant l'arboressence du namespace
	 *
	 * @param String $className
	 * @return boolean
	 */
	public static function loadByNamespace($className)
	{
		$className = str_replace('\\', '/', $className);
		if(file_exists(ROOT_PATH.$className.'.php')) {
			include(ROOT_PATH.$className.'.php');
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Chargement des class sans namespace
	 *
	 * @param String $className
	 * @return boolean
	 */
	public static function loadClass($className)
	{
		$className = str_replace(array('\\', '/'), '', $className);
		if(file_exists(ROOT_PATH.'Class/'.$className.'.php')) {
			include(ROOT_PATH.'Class/'.$className.'.php');
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Chargement des class sans namespace dans le répertoire phpmailer
	 * avec class. en préfixe de fichier
	 *
	 * @param String $className
	 * @return boolean
	 */
	public static function loadPrefix($className)
	{
		$className = str_replace(array('\\', '/'), '', $className);
		if(file_exists(ROOT_PATH.'Class/phpmailer/class.'.$className.'.php')) {
			include(ROOT_PATH.'Class/phpmailer/class.'.$className.'.php');
			return true;
		} else {
			return false;
		}
	}
}

spl_autoload_register('\Config\Autoload::loadByNamespace');
spl_autoload_register('\Config\Autoload::loadClass');
spl_autoload_register('\Config\Autoload::loadPrefix');