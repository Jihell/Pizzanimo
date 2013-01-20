<?php
/**
 * Class créant des requète de recherche
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace He\ORM\Part;
use He\Param;
use He\ORM\PDO;

class Find
{
	/**
	 * Nom de la table de donnée
	 *
	 * @var string
	 */
	protected $_name = '';
	
	/**
	 * Liste des colonnes de la table
	 *
	 * @var array
	 */
	protected $_param = array();
	
	/**
	 * Liste des lignes chargés
	 *
	 * @var array()
	 */
	protected $_row = array();
	
	/**
	 * Récupère les paramètres de la table, tel que la liste des colonnes
	 * 
	 * @return Table
	 */
	protected function _bindParam()
	{
		$this->_param = Param::getTableInfo($this->_name, $this->_database->getConnexion());
		return $this;
	}
	
	/**
	 * Get table name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Fonctions de récupération des lignes
	 */
	
	/**
	 * Récupère une ligne unique d'id $row_id
	 *
	 * @param integer $row_id
	 * @param integer $primaryIndex Numéro de clef primaire de référence
	 * @return \He\ORM\Table\Row 
	 */
	public function find($row_id, $primaryIndex = 0) {
		if(!isset($this->_row[$row_id])) {
			$query = 'SELECT '.$this->_param->getColumnList()
				.' FROM '.$this->_name
				.' WHERE '.$this->_param->getPrimary($primaryIndex).' = :id LIMIT 0,1';
		
			return $this->_loadFirst($query, array(':id' => $row_id));
		} else {
			return $this->_row[$row_id];
		}
	}
	
	/**
	 * Récupère toute les lignes
	 *
	 * @param string $orderBy
	 * @return \He\ORM\Table\Row 
	 */
	public function findAll($orderBy = '') {
		$query = 'SELECT '.$this->_param->getColumnList()
			.' FROM '.$this->_name
			.($orderBy != '' ? ' ORDER BY '.$orderBy : '');

		return $this->_loadArray($query);
	}
	
	/**
	 * Find all row by $criteria order by $orderBy in the limit of $limit.
	 * $criteria and $orderBy must have on key the column name.
	 * Limit take two value : begin and end.
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param array $limit
	 * @return array 
	 */
	public function findBy($criteria = array(), $orderBy = array(), $limit = array())
	{
		$query = 'SELECT '.$this->_param->getColumnList()
			.' FROM '.$this->_name.' WHERE ';
		
		$param = array();
		// Adding criterias
		if(count($criteria)) {
			foreach($criteria AS $column => $criteria) {
				$query .= $column.' = :'.$column.' AND ';
				$param[$column] = $criteria;
			}
			$query = substr($query, 0, -4);
		} else {
			$query = substr($query, 0, -6);
		}
		
		// Adding order by
		if(count($orderBy)) {
			$query .= ' ORDER BY ';
			foreach($orderBy AS $column => $order) {
				$query .= $column.' '.$order.', ';
			}
			$query = substr($query, 0, -2);
		}
		
		// Adding limit
		if(count($limit)) {
			$query .= ' LIMIT '.$limit['begin'].', '.$limit['end'];
		}
		
		return $this->_loadArray($query, $param);
	}
	
	/**
	 * Find the first row by $criteria order by $orderBy.
	 * $criteria and $orderBy must have on key the column name.
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @return array 
	 */
	public function findOneBy($criteria = array(), $orderBy = array())
	{
		$query = 'SELECT '.$this->_param->getColumnList()
			.' FROM '.$this->_name.' WHERE ';
		
		// Adding criterias
		if(count($criteria)) {
			$param = array();
			foreach($criteria AS $column => $criteria) {
				$query .= $column.' = :'.$column.' AND ';
				$param[$column] = $criteria;
			}
			$query = substr($query, 0, -4);
		}
		
		// Adding order by
		if(count($orderBy)) {
			$query .= ' ORDER BY ';
			foreach($orderBy AS $column => $order) {
				$query .= $column.' '.$order.', ';
			}
			$query = substr($query, 0, -2);
		}
		
		$query .= ' LIMIT 0, 1';
		
		return $this->_loadFirst($query, $param);
	}
	
	/**
	 * Execute a select query to return only the first row
	 *
	 * @param string $query
	 * @param array $parameters
	 * @return \He\ORM\Table\Row 
	 */
	protected function _loadFirst($query, $parameters = array())
	{
		$res = $this->_execute($query, $parameters);
		
		if(count($res)) {
			// Recherche d'une combinaison pour la clef primaire
			$primary = $this->_param->getPrimaryList();
			$val = '';
			foreach($primary AS $p) {
				$meth = 'get'.ucfirst($p);
				$val .= $res[0]->$meth().'*';
			}
			$val = crc32(substr($val, 0, -1));
			
			$this->_row[$val] = $res[0];
			return $this->_row[$val];
		} else {
			return;
		}
	}
	
	/**
	 * Execute a select query to return an array of results
	 *
	 * @param string $query
	 * @param array $parameters
	 * @return array
	 */
	protected function _loadArray($query, $parameters = array())
	{
		$res = $this->_execute($query, $parameters);
		
		if(count($res)) {
			// Enregitrement des résultats pour éviter un rechargement
			foreach($res AS $data) {
				// Recherche d'une combinaison pour la clef primaire
				$primary = $this->_param->getPrimaryList();
				$val = '';
				foreach($primary AS $p) {
					$meth = 'get'.ucfirst($p);
					$val .= $data->$meth().'*';
				}
				$val = crc32(substr($val, 0, -1));
				
				$this->_row[$val] = $data;
			}
			return $res;
		} else {
			return;
		}
	}
	
	/**
	 * Execute a select query
	 *
	 * @param string $query
	 * @param array $parameters
	 * @return \He\ORM\Table\Row 
	 */
	protected function _execute($query, $parameters = array())
	{
		if(!empty($query))
		{
			$sth = $this->_database->getConnexion()->prepare($query);
			$sth->execute($parameters);
			return $sth->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, '\He\ORM\Table\Row', array($this, $this->_param));
		}
	}
}