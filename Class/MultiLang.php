<?php
/**
 * Fichier contenant la classe {@link MultiLang}.
 *
 * @author Epoc <epoc@hotmail.fr>
 * @copyright Copyright (c) 2007-2008 Maxime "Epoc" G.
 */

/**
 * Classe pour g�rer plusieurs langues.
 *
 * Cette classe permet la gestion compl�te de plusieures langues � partir d'un
 * fichier (de type INI) contenant les textes.
 *
 * <b>Exemple d'utilisation :</b>
 * <code>
 * include('MultiLang.class.php'); // Inclusion du fichier de la classe
 *
 * // Instantation de la classe avec en param�tre :
 * // 1. Le code ISO 639 de la langue choisie.
 * // 2. Le r�pertoire qui contient les fichier INI de traduction.
 * $MultiLang = new MultiLang('EN', 'loc/');
 *
 * // Affiche "Search" ou "Rechercher" si FR et EN sont disponibles
 * echo $MultiLang->getMsg('MAIN_SEARCH');
 * </code>
 *
 * <b>Note :</b>
 * Le code ISO 639 sera plusieurs fois mentionn� dans cette documentation. Il
 * s'agit en fait d'un code universel qui permet d'identifier une langue.
 * Exemple : FR pour Fran�ais, EN pour English...
 *
 * @author Epoc <epoc@hotmail.fr>
 * @copyright Copyright (c) 2007-2008 Maxime "Epoc" G.
 * @version 11.02.2008
 */
class MultiLang {
	/**
	 * La langue choisie.
	 *
	 * Le code ISO 639 de la langue choisie. Vous trouverez la liste des codes
	 * ISO 639 des langues sur le site {@link http://www.loc.gov/standards/iso639-2/langcodes.html}.
	 *
	 * @var string
	 */
	private $language = NULL;

	/**
	 * Tableau qui contient les textes.
	 *
	 * Cette propri�t� contient un tableau avec les textes du langage choisi.
	 *
	 * @var array
	 */
	private $lang_array = NULL;

	/**
	 * Le r�pertoire qui contient les fichiers des messages (de type INI)
	 *
	 * @var string
	 */
	private $loc_dir = NULL;

	/**
	 * Constructeur de la classe.
	 *
	 * D�finis le language choisi et le chemin vers le r�pertoire des fichiers INI.
	 * D�finis le tableau des messages en fonction du fichier de la langue choisie.
	 *
	 * @param string $language Le language choisi (les deux premi�res lettres).
	 * @param string $loc_dir Le r�pertoire qui contient le fichier INI des textes.
	 * @uses langage
	 * @uses loc_dir
	 * @uses lang_array
	 */
	function __construct($language, $loc_dir) {
		$this->loc_dir = $loc_dir;
		$this->langage = strtoupper($language);

		$file = $this->loc_dir.$this->langage.'.ini';
		//echo ($this->lang_array);
		if (file_exists($file))
		{
			if (!isset($this->lang_array['LANG_NAME']))
			{
				$this->lang_array = parse_ini_file($file, TRUE);
			}
		}
	}

	/**
	 * Renvoie le texte d'un message.
	 *
	 * Cette fonction renvoie le texte du message $msgid qui peut �tre situ�
	 * dans une section $section.
	 *
	 * @param string $msgid La cl� du tableau {@link langarray} qui contient le message voulu.
	 * @param string $section (optionnel) La section dans laquelle est contenu le message $msgid.
	 * @return string Le message voulu ou la cl� appell�e ($msgid) si inexistant.
	 * @uses lang_array
	 */
	public function getMsg($msgid, $section = NULL) {
		//echo ($this->lang_array."<br/>");
		if(!is_array($this->lang_array)) {
			return $msgid;
		}
		if (!empty($section)) {
			if (array_key_exists($section, $this->lang_array)) {
				if (array_key_exists($msgid, $this->lang_array[$section])) {
					return /*htmlentities(*/$this->lang_array[$section][$msgid]/*, ENT_QUOTES)*/;
				} else {
					return $msgid;
				}
			} else {
				return $msgid;
			}
		} else {
			if (array_key_exists($msgid, $this->lang_array)) {
				return /*htmlentities(*/$this->lang_array[$msgid]/*, ENT_QUOTES)*/;
			} else {
				return $msgid;
			}
		}
	}

	/**
	 * Retourne tous les languages install�s.
	 *
	 * Fonction qui retourne sous forme de tableau la liste des languages disponibles qui
	 * contient en cl� le code ISO 639 de la langue et en valeur le nom de la langue.
	 * Effectu� � partir des fichier INI contenus par d�faut dans le dossier loc/.
	 *
	 * <b>Exemple :</b>
	 * <code>
	 * $langsavailables = $MultiLang->getLangagesAvailable(); // Langages dispos
	 *
	 * // Tri alphab�tique avec option de tri "comme des cha�nes de caract�res"
	 * asort($langsavailables, SORT_STRING);
	 *
	 * // Affichage des r�sultats pour chaques langages
	 * foreach ($langsavailables as $langid => $langname) {
	 *     echo $MultiLang->getLangFlag('htmlcode', $langid).' '.$langname.'<br>';
	 * }
	 * </code>
	 *
	 * @return array Tableau qui contient en cl� le code ISO 639 de la langue et en valeur le nom de la langue.
	 * @uses loc_dir
	 */
	public function getLangagesAvailable() {
		$langfiles = glob($this->loc_dir.'*.ini');
		foreach ($langfiles as $filename) {
		   $langfile = parse_ini_file($filename);

		   $langid = pathinfo($filename, PATHINFO_FILENAME);
		   $langname = $langfile['LANG_NAME'];

		   $langavailable[$langid] = $langname;
		}
		return $langavailable;
	}

	/**
	 * D�fini la langue active.
	 *
	 * Cette fonction d�fini la langue $language comme active.
	 *
	 * @param string $language Le code ISO 639 de la langue � d�finir comme active.
	 * @uses language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}

	/**
	 * Remplace une occurence dans un message.
	 *
	 * Sert � remplacer une occurence de texte $occur par $replace dans le message $msg.
	 * Retourne le message final.
	 *
	 * <b>Exemple d'utilisation :</b>
	 * <code>
	 * // Messages TEST_REPLACE d'origine :
	 * // FR : Il y a actuellement #TOTAL_NUM_COMS# commentaires au total dans
	 * // la base de donn�es.
	 * // EN : There is currently a total of #TOTAL_NUM_COMS# comments in the database.
	 * echo $MultiLang->replaceOccur('TOTAL_NUM_COMS', 12, 'TEST_REPLACE');
	 *
	 * // Affiche :
	 * // FR : Il y a actuellement 12 commentaires au total dans la base de donn�es.
	 * // EN : There is currently a total of 12 comments in the database.
	 * </code>
	 *
	 * <b>Attention :</b>
	 * Cette fonction recherche toute l'occurence +$occur+ pour la remplacer enti�rement.
	 *
	 * @param string $occur L'occurence � remplacer.
	 * @param string $replace La valeur de remplacement.
	 * @param string $msgid L'identifiant du message dans lequel il faut rechercher $occur.
	 * @return string Le message final.
	 */
	public function replaceOccur($occur, $replace, $msgid) {
		return str_replace('#'.$occur.'#', $replace, $this->getMsg($msgid));
	}
        
        
        
        
        
        
        /**
	 * Remplace une liste d'occurence dans un message.
	 *
	 * Sert à remplacer des occurences de texte $occur par $replace dans le message $msg.
	 * Retourne le message final.
	 *
	 * <b>Exemple d'utilisation :</b>
	 * <code>
	 * // Messages TEST_REPLACE d'origine :
	 * // FR : Il y a actuellement #TOTAL_NUM_COMS# commentaires pour #TOTAL_USERS# utilisateurs au total dans
	 * // la base de données.
	 * // EN : There is currently a total of #TOTAL_NUM_COMS# comments for #TOTAL_USERS# users in the database.
	 * echo $MultiLang->replaceOccur(array('TOTAL_NUM_COMS'=>12, 'TOTAL_USERS'=>15), 'TEST_REPLACE');
	 *
	 * // Affiche :
	 * // FR : Il y a actuellement 12 commentaires pour 15 utilisateurs au total dans la base de données.
	 * // EN : There is currently a total of 12 comments for 15 users in the database.
	 * </code>
	 *
	 * <b>Attention :</b>
	 * Cette fonction recherche toute l'occurence +$occur+ pour la remplacer entièrement.
	 *
	 * @param array $array_occur Liste les couples occurence/remplacement 
	 * @param string $msgid L'identifiant du message dans lequel il faut rechercher les occurences $array_occur.
	 * @return string Le message final.
	 */
        public function replaceMultipleOccur($array_occur, $msgid) 
        {
            $msg = $this->getMsg($msgid);
            foreach ($array_occur as $occur=>$replace)
            {
                $msg = str_replace('#'.$occur.'#', $replace, $msg);
            }
            return $msg;
        }
        
        
        

}
?>