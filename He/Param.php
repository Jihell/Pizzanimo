<?php
/**
 * Liste des paramètes de l'application :
 * - Répertoire d'origine de l'application
 * - Liste des bases de données disponibles
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace He;

class Param
{
	/**
	 * Liste des paramètres bases de données
	 *
	 * @var array
	 */
	private static $_databases = array();
	
	/**
	 * Liste des paramètres des tables
	 *
	 * @var array
	 */
	private static $_table = array();
	
	/**
	 * Retourne le répertoire racine du site
	 *
	 * @return string
	 */
	public static function getRoot() {
		$path = str_replace('\\', '/', __DIR__);
		$path = explode('/',$path);
		return \substr(__DIR__, 0, -\strlen(\array_pop($path)));
	}
	
	/**
	 * Ajout une base de donnée à la liste des paramètre de l'application
	 *
	 * @param \He\Param\DataBase $db
	 * @return \He\Param\DataBase
	 * @throw \He\Exception 
	 */
	public static function addDataBase(Param\DataBase $db) {
		if(!static::hasDataBase($db->getAlias())) {
			static::$_databases[$db->getAlias()] = $db;
			return $db;
		} else {
			throw new Exception('La base de donnée "'.$db->getAlias().'" est déjà déclaré !');
		}
	}
	
	/**
	 * Récupère les paramètres de la base de donnée $alias
	 *
	 * @param string $alias
	 * @return Param\DataBase
	 */
	public static function getDataBase($alias) {
		if(static::hasDataBase($alias)) {
			return static::$_databases[$alias];
		} else {
			throw new Exception('Les paramètres de la base de donnée "'.$alias.'" n\'existent pas');
		}
	}
	
	/**
	 * Test si la base de donnée $alias est déjà déclaré
	 *
	 * @param string $alias
	 * @return boolean
	 */
	public static function hasDataBase($alias)
	{
		return array_key_exists($alias, static::$_databases);
	}
	
	/**
	 * Récupère l'URI courante
	 *
	 * @return string
	 */
	public static function getRequestedUri()
	{
		return $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Test if the given uri is the current adress, set $orNull to true to
	 * valid the / path
	 *
	 * @param string $uri
	 * @param boolean $orNull
	 * @return boolean
	 */
	public static function isActivePage($uri, $orNull = false)
	{
		return static::getRequestedUri() == $uri || 
			static::getRequestedUri() == '/' && $orNull;
	}
	
	/**
	 * Test if the given uri tab contain the current adress, set $orNull to true to
	 * valid the / path
	 *
	 * @param array $array
	 * @param boolean $orNull
	 * @return boolean
	 */
	public static function isPageInArray($array = array(), $orNull = false)
	{
		return in_array(static::getRequestedUri(), $array) || 
			static::getRequestedUri() == '/' && $orNull;
	}
	
	/**
	 * Créer une chaine de caractère avec le nom du serveur
	 *
	 * @return string
	 */
	public static function getServerName() {
		return 'http'.(isset($_SERVER['HTTPS']) ? 's' : '')
			.'://'.$_SERVER['SERVER_NAME']
			.':'.$_SERVER['SERVER_PORT'].'/';
	}
	
	/**
	 * Créer une chaine de caractère avec le nom du serveur dans le sous domaine
	 *
	 * @return string
	 */
	public static function getServerAddress() {
		list($sub, $domain, $ttl) = explode('.', $_SERVER['SERVER_NAME']);
		
		return $domain.'.'.$ttl;
	}
	
	/**
	 * Renvoi les paramètres de la table $tableName tel que la liste des
	 * colonnes
	 *
	 * @param string $tableName
	 * @param \He\PDO $connexion 
	 */
	public static function getTableInfo($tableName, \He\ORM\PDO $connexion)
	{
		if(!static::hasTable($tableName)) {
			static::$_table[$tableName] = new Param\Table($tableName, $connexion);
		}
		
		return static::$_table[$tableName];
	}
	
	/**
	 * Test si la table $tableName est chargé
	 *
	 * @param string $tableName
	 * @return boolean
	 */
	public static function hasTable($tableName)
	{
		return array_key_exists($tableName, static::$_table);
	}
}