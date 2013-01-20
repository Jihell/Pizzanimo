<?php
/**
 * storHighscore
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */

$required = array('pseudo', 'niveau', 'points', 'combo', 'block', 'single');

if(!empty($_POST)) {
	// Analyse de pertinence du formulaire
	foreach($required AS $field) {
		if(!isset($_POST[$field])) {
			echo 'Erreur : les champs sont mal remplis';
			exit(200);
		}
	}
	
	// Création de l'entrée
	$newRow = \He\ORM::getDatabase('main')->getTable('highscore')->newRow();
	$newRow->setPseudo($_POST['pseudo'])
		->setNiveau($_POST['niveau'])
		->setPoints($_POST['points'])
		->setCombo($_POST['combo'])
		->setBlock($_POST['block'])
		->setSingle($_POST['single'])
		->setCreated_at(date('Y-m-d H:i:s'))
	;
	$newRow->stor();
	
	echo 'Enregistrement effectué !';
	exit(200);
}

echo 'Erreur : Vous n\'êtes pas autorisé à accéder à cette page !';
exit(500);