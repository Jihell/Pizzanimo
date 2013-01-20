<?php
/**
 * DataBase
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace He\Param;

class DataBase
{
	/**
	 * Alias de la base de donnée
	 *
	 * @var string
	 */
	private $_alias = '';
	
	/**
	 * Nom de la base de donnée
	 *
	 * @var string
	 */
	private $_bdd_name = '';
	
	/**
	 * Host de la base de donnée
	 *
	 * @var string
	 */
	private $_bdd_host = '';
	
	/**
	 * Utilisateur de la base de donnée
	 *
	 * @var string
	 */
	private $_bdd_user = '';
	
	/**
	 * Mot de passe de la base de donnée
	 *
	 * @var string 
	 */
	private $_bdd_pswd = '';
	
	/**
	 * Prépare le paramétrage de la base de donnée avec son alias.
	 *
	 * @param type $alias 
	 */
	public function __construct($alias) {
		$this->_alias = $alias;
	}
	
	/**
	 * Get parameter $_alias
	 *
	 * @return string
	 */
	public function getAlias() {
		return $this->_alias;
	}
	
	/**
	 * Get parameter $_bdd_name
	 *
	 * @return string
	 */
	public function get_bdd_name()
	{
		return $this->_bdd_name;
	}

	/**
	 * Set the parameter $_bdd_name
	 *
	 * @param string $_bdd_name 
	 * @return DataBase
	 */
	public function set_bdd_name($_bdd_name)
	{
		$this->_bdd_name = $_bdd_name;
		return $this;
	}
	
	/**
	 * Get parameter $_bdd_host
	 *
	 * @return string
	 */
	public function get_bdd_host()
	{
		return $this->_bdd_host;
	}

	/**
	 * Set the parameter $_bdd_host
	 *
	 * @param string $_bdd_name 
	 * @return DataBase
	 */
	public function set_bdd_host($_bdd_host)
	{
		$this->_bdd_host = $_bdd_host;
		return $this;
	}

	/**
	 * Get parameter $_bdd_user
	 *
	 * @return string
	 */
	public function get_bdd_user()
	{
		return $this->_bdd_user;
	}

	/**
	 * Set the parameter $_bdd_user
	 *
	 * @param string $_bdd_name 
	 * @return DataBase
	 */
	public function set_bdd_user($_bdd_user)
	{
		$this->_bdd_user = $_bdd_user;
		return $this;
	}

	/**
	 * Get parameter $_bdd_pswd
	 *
	 * @return string
	 */
	public function get_bdd_pswd()
	{
		return $this->_bdd_pswd;
	}

	/**
	 * Set the parameter $_bdd_pswd
	 *
	 * @param string $_bdd_name 
	 * @return DataBase
	 */
	public function set_bdd_pswd($_bdd_pswd)
	{
		$this->_bdd_pswd = $_bdd_pswd;
		return $this;
	}
}