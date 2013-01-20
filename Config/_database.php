<?php
/**
 * Configuration des bases de donnée.
 * Pour chaque base, ajouter de la manière suivante :
 * 
 * Param::addDataBase(new Param\Database(ALIAS))
 *		->set_bdd_host(HOST)
 *		->set_bdd_name(NAME)
 *		->set_bdd_user(USER)
 *		->set_bdd_pswd(PASSWORD)
 *		;
 * 
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */

namespace Config;

// Local
\He\Param::addDataBase(new \He\Param\DataBase('main'))
		->set_bdd_host('localhost')
		->set_bdd_name('pjc_pizzanimo')
		->set_bdd_user('root')
		->set_bdd_pswd('')
		;