<?php

/**
 * Description of Table
 *
 * @author joseph lemoine - joseph.lemoine@fluedis.com
 */
namespace He\Param;

class Table {
	/**
	 * Nom de la table
	 *
	 * @var string
	 */
	protected $_name = '';
	
	/**
	 * Liste des colonnes de la table
	 *
	 * @var array
	 */
	protected $_columns = array();
	
	/**
	 * Chaine de caractère représentatnt la liste des champs de la table courante
	 * séparés par des virgules
	 *
	 * @var string
	 */
	protected $_columnList = '';
	
	/**
	 * Chaine de caractère représentatnt la liste des champs de la table courante
	 * séparés par des virgules et précédés par :
	 *
	 * @var string
	 */
	protected $_columnListReplace = '';
	
	/**
	 * Liste des jointures possibles
	 *
	 * @var array
	 */
	protected $_join = array();
	
	/**
	 * Liste des clefs primaires
	 *
	 * @var array
	 */
	protected $_primary = array();
	
	public function __construct($name, \He\ORM\PDO $connexion)
	{
		$this->_name = $name;
		
		$this->_loadColumns($connexion);
	}
	
	/**
	 * Charge les colonnes de la table
	 *
	 * @param \He\ORM\PDO $connexion 
	 */
	protected function _loadColumns(\He\ORM\PDO $connexion)
	{
		$colist = $connexion->query('SHOW COLUMNS FROM '.$this->_name)->fetchAll();
		
		if(count($colist)) {
			foreach($colist AS $row => $col) {
				// Ajout de la colonne à la liste
				$this->_columns[] = $col['Field'];
				$this->_columnList .= $col['Field'].', ';
				$this->_columnListReplace .= ':'.$col['Field'].', ';
				
				// Ajout des clefs primaires
				if($col['Key'] == 'PRI') {
					$this->_primary[] = $col['Field'];
				}
				
				// Ajout des clefs étrangères, pour rappel elles doivent être
				// Syntaxés comme ceci : id_[table] exemple : id_test
				if(substr($col['Field'], 0, 3) == 'id_') {
					$this->_join[$col['Field']] = substr($col['Field'], 3);
				}
			}
			
			$this->_columnList = substr($this->_columnList, 0, -2);
			$this->_columnListReplace = substr($this->_columnListReplace, 0, -2);
		}
	}
	
	/**
	 * Verify is a column is linked on another table
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function isLinked($name)
	{
		return in_array($name, $this->_join);
	}
	
	/**
	 * Recherche la colonne $name dans le table des colonnes possibles
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function hasColumn($name)
	{
		return in_array($name, $this->_columns);
	}
	
	/**
	 * Récupère la liste des colonnes de la table
	 *
	 * @return array()
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
	/**
	 * Récupère la liste des colonnes
	 *
	 * @return string
	 */
	public function getColumnList()
	{
		return $this->_columnList;
	}
	
	/**
	 * Récupère la liste des colonnes pour les insertions BDD
	 *
	 * @return string
	 */
	public function getColumnListReplace()
	{
		return $this->_columnListReplace;
	}
	
	/**
	 * Récupère la clef primaire à la position $index
	 *
	 * @param integer $index
	 * @return string
	 */
	public function getPrimary($index)
	{
		if(isset($this->_primary[$index])) {
			return $this->_primary[$index];
		} else {
			throw new \Exception('Pas de clef primaire en position '.$index.' de la table '.$this->_name);
		}
	}
	
	/**
	 * Récupère la liste des clefs primaires
	 *
	 * @return array
	 */
	public function getPrimaryList()
	{
		return $this->_primary;
	}
	
	/**
	 * Test si la colonne $name est une clef primaire
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function isPrimary($name)
	{
		return in_array($name, $this->_primary);
	}
}