<?php
/**
 * Instance standard d'une ligne d'un table de base de donnée
 *
 * @author joseph lemoine - joseph.lemoine@fluedis.com
 */
namespace He\ORM\Table;
use He\Trace;

class Row
{
	/**
	 * Table parente
	 * 
	 * @var \He\ORM\Table
	 */
	protected $_table;
	
	/**
	 * Paramètres de la table parente
	 * 
	 * @var \He\Param\Table
	 */
	protected $_param;
	
	/**
	 * Définie si la ligne est en base ou non
	 *
	 * @var boolean
	 */
	protected $_isNewRow = false;
	
	/**
	 * Enregistrement des valeurs des différentes colonnes de la ligne
	 * $_data[colonne] = valeur
	 *
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * Ajoute les paramètre de l'instance
	 * 
	 * @param \He\ORM\Table $table
	 * @param \He\Param\Table $param
	 * @param boolean $newRow
	 */
	public function __construct($table, $param, $newRow = false)
	{
		$this->_table = $table;
		$this->_param = $param;
		$this->_isNewRow = $newRow;
	}
	
	/**
	 * Récupère la valeur d'une colonne $name
	 *
	 * @param string $name
	 * @return mixed 
	 */
	public function __get($name) {
		if($this->_param->hasColumn($name)) {
			return $this->_data[$name];
		} else {
			throw new \Exception('La colonne '.$name.' de la table '.$this->_table->getName().' n\'existe pas !');
		}
	}

	/**
	 * Définie la valeur de la colonne $name
	 *
	 * @param string $name
	 * @param mixed $value 
	 */
	public function __set($name, $value) {
		if($this->_param->hasColumn($name)) {
			$this->_data[$name] = $value;
		} else {
			throw new \Exception('La colonne '.$name.' de la table '.$this->_table->getName().' n\'existe pas');
		}
	}
	
	/**
	 * Réecriture des getters et setters automatique
	 * Les setters renvoient $this pour enchainer
	 *
	 * @param string $name
	 * @param array $arguments 
	 */
	public function __call($name, $arguments) {
		$name = strtolower($name);
		
		// Si c'est un getter
		if(substr($name, 0, 3) == 'get') {
			$prop = substr($name, 3);
			return $this->$prop;
			
		// Sinon si c'est un setter
		} elseif(substr($name, 0, 3) == 'set') {
			$prop = substr($name, 3);
			$this->$prop = $arguments[0];
			return $this;
			
		// Sinon si c'est une jointure
		} elseif(substr($name, 0, 4) == 'join') {
			$prop = substr($name, 4);
			return $this->_join($prop);
		}
	}
	
	protected function _join($table)
	{
		if($this->_param->isLinked($table)) {
			$column = 'id_'.$table;
			return $this->_table->getDatabase()->getTable($table)->find($this->$column);
		} else {
			throw new Exception('Cette colonne ne possède pas de jointures vers '.$table.' dans '.$this->_table->getName().' !');
		}
	}
	
	/**
	 * Enregistre ou update la ligne en base de donnée
	 */
	public function stor()
	{
		$primary = $this->_param->getPrimary(0);
		$method = 'get'.ucfirst($primary);
			
		// Initialisation des paramètres
		$param = array();
		
		// Si on a une valeur sur la première clef primare, on update
		if(!$this->_isNewRow) {
			Trace::addTrace('Mise à jour de la table '.$this->_table->getName()
				.', ligne ('.$primary.') '
				.$this->$method(), __CLASS__);
			
			// Ecriture de la requète
			$sql = 'UPDATE '.$this->_table->getName().' SET ';
			foreach($this->_param->getColumns() AS $name) {
				$sql .= $name.' = :'.$name.', ';
				$param[':'.$name] = $this->$name;
			}
			$sql = substr($sql, 0, -2);
			
			// Clause where (pour chaque clef primaire)
			$sql .= ' WHERE ';
			foreach($this->_param->getPrimaryList() AS $key) {
				$sql .= $key.' = :'.$key.', ';
				$param[':'.$key] = $this->$key;
			}
			$sql = substr($sql, 0, -2);
		} else {
			$sql = 'INSERT INTO '.$this->_table->getName().' VALUES('
				.$this->_param->getColumnListReplace().');';
			foreach($this->_param->getColumns() AS $name) {
				$param[':'.$name] = $this->$name;
			}
		}
		
		$sth = $this->_table->getDatabase()->getConnexion()->prepare($sql);
		$sth->execute($param);
		
		// Si on vient d'enregistrer cette ligne, on lui donne son ID
		if(!$this->$method()) {
			$this->$primary = $this->_table->getDatabase()->getConnexion()->lastInsertId($this->_table->getName());
			\He\Trace::addTrace('Ajout d\'une nouvelle ligne : '.$this->$primary, __CLASS__);
		}
	}
}