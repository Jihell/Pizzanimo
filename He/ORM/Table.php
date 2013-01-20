<?php
/**
 * Class d'accès à une table de donnée
 *
 * @author joseph lemoine - joseph.lemoine@fluedis.com
 */
namespace He\ORM;
use He\ORM\Part\Find;
use He\Param AS Param;
use He\ORM\Table\Row;

class Table extends Find
{
	/**
	 * Base de donnée associé à cette table
	 *
	 * @var \He\ORM\DataBase
	 */
	protected $_database;
	
	/**
	 * Créer une nouvelle instance paramétré pour la table $name
	 *
	 * @param string $name
	 * @param \He\ORM\DataBase $database 
	 */
	public function __construct($name, \He\ORM\DataBase $database)
	{
		$this->_name = $name;
		$this->_database = $database;
		
		$this->_bindParam();
	}
	
	/**
	 * Get the database
	 *
	 * @return type 
	 */
	public function getDatabase()
	{
		return $this->_database;
	}
	
	/**
	 * Créer une nouvelle ligne
	 * 
	 * @return \He\ORM\Table\Row
	 */
	public function newRow()
	{
		return new Row($this, $this->_param, true);
	}
}