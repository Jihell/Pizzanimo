<?php
/**
 * Instance d'interface PDO
 * @author : Joseph Lemoine - lemoine.joseph@gmail.com
 * @version : 6
*/
namespace He\ORM;
use He\Trace;

final class PDO extends \PDO {

    /**
     * Stockage de l'objet PDO instancié
     * @var	array	(dnsBase => Objet)
     * @static
     */
    private static $_instance = array();

    /**
     * Compteur de requète pour le débug
     * @var	int
     * @static
     */
    public static $_benchmark = 0;

    /**
     * Listing des requètes pour le débug
     * @var	string
     * @static
     */
    public static $_history = '';
	
	/**
	 * Tableau de stockage des requètes préparés
	 * @var array
	 */
	private static $_storedSTH = array();

    /**
     * constructeur de class. Privé pour éviter les accès extérieur
     * @return $this;
     */
    public function __construct($bdd, $user, $pass, $options = array()){
		Trace::addTrace('Création de l\'instance PDO', __CLASS__);
		
		$options += array(static::ATTR_ERRMODE => self::ERRMODE_EXCEPTION,
						 static::ATTR_STATEMENT_CLASS => array('\He\ORM\PDOStatement'));
		
		return parent::__construct($bdd, $user, $pass, $options);
    }

    /**
     * Renvois de l'instance PDO et init si requis avec gestion des erreurs
     * @param	string	$alias	Alias de base de donnée
     * @return	\He\PDO
    */
    public static function getInstance(\He\Param\DataBase $param) {
	
		/* Si la base n'à pas encore été instancié, on la crée */
		if(!isset(static::$_instance[$param->getAlias()]))
		{
		    try {
				/* Création de la base PDO */
				static::$_instance[$param->getAlias()] = new static('mysql:dbname='.$param->get_bdd_name()
				.';host='.$param->get_bdd_host().';', // DNS
				$param->get_bdd_user(), // User
				$param->get_bdd_pswd());
				
				/* On force l'utilisation de l'UTF-8 */
				static::$_instance[$param->getAlias()]->exec('SET NAMES utf8');
		    }catch(PDOException $e) {
				\He\Trace::addTrace('Connexion échouée : '.$e->getMessage());
				exit();
		    }
	
		}
	
		return static::$_instance[$param->getAlias()];
    }

    /**
	 * Exécute une requète en surchargeant PDO
	 * @param	string	$statement	Requète SQL à exécuter
	 * @return	PDOStatement
     */
    public function query($statement)
	{
		try
		{
			Trace::addTrace('Requète BDD : '.substr($statement,0,2000), __CLASS__);
			
			$sth = $this->prepare($statement);
			$sth->execute();
			return $sth;
		}
		catch(PDOException $e)
		{
			echo '<h2>CRASH !</h2>'
				.'<p>'.$statement.'</p>'
				.'<p>'.$e->getMessage().'</p>';
			exit();
		}
    }
	
	/**
	 * Alias static de quote, protège des injections SQL et fait attention 
	 * aux fonctions de mysql
	 * @param string $val
	 * @param string $param
	 * @return string
	 */
	public static function bind($val, $param = \He\PDO::PARAM_STR)
	{
		if(is_null($val))
			return 'NULL';
		if($val == 'NOW()')
			return $val;
		
		return static::getInstance()->quote($val, $param);
	}
	
	/**
	 * Surcharge de PDO->prepare pour gérer les erreurs
	 * @param	string	$query	requète à exécuter
	 * @return	OBJ				PDOStatement
	 */
	public function prepare($statement)
	{
		$hash = crc32($statement);
		
		if(empty(static::$_storedSTH[$hash]))
		{
			try
			{
				Trace::addTrace('Préparation de requète BDD : '.substr($statement,0,2000).' (HASH : '.$hash.') ', __CLASS__);
				static::$_storedSTH[$hash] = parent::prepare($statement, array(PDO::ATTR_CURSOR, PDO::CURSOR_FWDONLY));
			}
			catch(PDOException $e)
			{
				echo 'ARGH CRASH !'.$e->getMessage();
				exit();
			}
		}

		return static::$_storedSTH[$hash];
	}
	
    /**
     * Alias pour faire un fatch all après une requète sans créer de DAO
     * @param	string	$query Requète SQL à exécuter
     * @param	string	$alias	Nom de la base de donnée à appeler
     * @return	array	Résultat de la requète dans un array	
     * @static
     */
	public static function run($query, $alias = ''){
		return static::getInstance($alias)->query($query)->fetchAll();
    }
}
?>