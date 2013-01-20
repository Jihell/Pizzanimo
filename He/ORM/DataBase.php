<?php
/**
 * DataBase
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace He\ORM;

class DataBase
{
	/**
	 * Paramètre de connection à la base de donnée
	 *
	 * @var \He\Param\DataBase
	 */
	private $_param;
	
	/**
	 * Instance PDO lié à cette base de donnée
	 *
	 * @var PDO
	 */
	private $_connexion;
	
	/**
	 * Liste des tables chargés
	 *
	 * @var array
	 */
	private $_table = array();
	
	/**
	 * 
	 *
	 * @param \He\Param\DataBase $param 
	 */
	public function __construct(\He\Param\DataBase $param) {
		$this->_param = $param;
		
		// Création de la connection via PDO
		$this->_connexion = \He\ORM\PDO::getInstance($param);
	}
	
	/**
	 * Renvoi la connexion PDO lié à cette base de donnée
	 *
	 * @return \He\PDO
	 */
	public function getConnexion()
	{
		return $this->_connexion;
	}
	
	/**
	 * Renvoi la class d'abstraction propre à la table demandé,
	 * alias de getTable
	 *
	 * @param string $tableName
	 * @return \He\ORM\Table
	 */
	public function __call($tableName, $arguments)
	{
		return $this->getTable($tableName);
	}
	
	/**
	 * Renvoi la class d'abstraction propre à la table demandé
	 *
	 * @param string $tableName
	 * @return \He\ORM\Table
	 */
	public function getTable($tableName)
	{
		if(!$this->hasTable($tableName)) {
			$this->_table[$tableName] = new \He\ORM\Table($tableName, $this);
		}
		
		return $this->_table[$tableName];
	}
	
	/**
	 * Test si une table est chargé dans la base de donnée
	 *
	 * @param type $tableName
	 * @return type 
	 */
	public function hasTable($tableName)
	{
		return array_key_exists($tableName, $this->_table);
	}
	
	/**
	 * Test si un table est présente dans la base MySQL
	 *
	 * @param string $tableName 
	 * @return integer
	 */
	public function tableExist($tableName) {
		$sql = 'SHOW TABLES FROM '.$this->_param->get_bdd_name().' LIKE \''.$tableName.'\'';
		$sth = $this->getConnexion()->prepare($sql);
		$sth->execute();
		$res = $sth->fetch();
		
		return is_array($res);
	}
	
	/**
	 * Insère des commande SQL depuis un fichier, remplace les occurences de
	 * {TABLE} dans le fichier par $tableName
	 *
	 * @param string $filename
	 * @param string $tableName
	 * @return boolean 
	 */
	public function injectSQL($filename, $tableName)
	{
		$sql = str_replace('{TABLE}', $tableName, file_get_contents($filename));
		$sql = str_replace('{DATABASE}', $this->_param->get_bdd_name(), $sql);
		$sth = $this->getConnexion()->prepare($sql);
		return $sth->execute();
	}
}