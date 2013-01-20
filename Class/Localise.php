<?php
/**
 * Analyse the string sended to auto replace all the localisation key found.
 * The search pattern have the form '{%LOCALISATION_KEY%}'
 *
 * =============================================================================
 * COMMON USAGE
 * =============================================================================
 * 
 * 
 * 
 * @author joseph lemoine - joseph.lemoine@fluedis.com
 * @copyright fluedis
 */
use He\Trace;

class Localise
{	
	private static $_multiLangInstance = array();
	
	/**
	 * This class is static only
	 */
	private function __construct() {}
	private function __clone() {}
	
	private static function _getMultiLang($lang = null)
	{
		$used_lang = empty($lang) ? $_SESSION["lang"] : $lang;
		
		if(!static::$_multiLangInstance[$used_lang])
		{
			$_SESSION["lang"] = empty($_SESSION["lang"]) ? DEFAULT_LANG : $_SESSION["lang"];
			static::$_multiLangInstance[$used_lang] = new MultiLang($used_lang, ROOT_PATH."lang/");
		}
		
		return static::$_multiLangInstance[$used_lang];
	}
	
	/**
	 * Analyse a string and replace the vars from the localisation ini files
	 * @param type $xml
	 * @return type 
	 */
	public static function run(&$xml, $lang = null)
	{
		if(empty($lang) && !empty($_SESSION["lang"]))
		{
			$lang = $_SESSION["lang"];
		}
		else
		{
			$lang = empty($lang) ? DEFAULT_LANG : $lang;
		}
		
		Trace::addTrace('Début de localisation de la page', get_called_class());
		if(!empty($xml))
		{
			/* Supression des commentaires */
			$pattern = '#{\*(.*?)\*}#s';
			preg_match_all($pattern, $xml, $matches);

			/* Si on à bien des résultats */
			if (!empty($matches[1]))
			{
				foreach ($matches[1] AS $key => $comment)
				{
					/* Suppression de la balise */
					$xml = str_replace( '{*'.$comment.'*}', '', $xml);
				}
			}
			
			/* Localisation */
			preg_match_all('#{\%([^}]*)}#', $xml, $matches);
			if(!empty($matches[1]))
			{
				foreach($matches[1] AS $row => $key)
				{
					$xml = str_replace($matches[0][$row], static::_getMultiLang($lang)->getMsg($key), $xml);
				}
			}
			Trace::addTrace('Page localisé !', get_called_class());
			return $xml;
		}
		
		Trace::addTrace('Donnée vides, impossible de localiser', get_called_class());
		return false;
	}
	
	public static function get($key, $lang = null)
	{
		if(!empty($key))
		{
			return static::_getMultiLang($lang)->getMsg($key);
		}
	}
}