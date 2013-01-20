<?php
/**
 * ORM
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */
namespace He;

class ORM
{
	static $_databases = array();
	
	/**
	 * Récupère la classe d'abstraction correspondant à la BDD demandé, si elle
	 * n'esite pas, elle est alors instancié.
	 *
	 * @param string $alias
	 * @return \He\ORM\DataBase
	 */
	public static function getDatabase($alias) {
		if(!static::hasDataBase($alias)) {
			static::$_databases[$alias] = new ORM\DataBase(Param::getDataBase($alias));
		}
		
		return static::$_databases[$alias];
	}
	
	/**
	 * Test si la connection à la base de donnée $alias est déjà déclaré
	 *
	 * @param string $alias
	 * @return boolean
	 */
	public static function hasDataBase($alias) {
		return array_key_exists($alias, static::$_databases);
	}
}