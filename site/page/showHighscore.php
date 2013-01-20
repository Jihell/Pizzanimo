<?php
/**
 * showHighscore
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */

if(!empty($_POST)) {
	$menu = $_POST['menu'];
	
	$res = \He\ORM::getDatabase('main')->getTable('highscore')->findBy(
		array(), 
		array($menu => 'DESC'), 
		array('begin' => 0, 'end' => 5)
	);
	
	$meth = 'get'.ucfirst($menu);
	$send = '';
	if(count($res) > 0) {
		foreach($res AS $row) {
			$send .= '<li>'
					.'<span class="name">'.$row->getPseudo().'</span>'
					.'<span class="score">'.$row->$meth().'</span>'
				.'</li>';
		}
	}
	for($i = count($res); $i < 5; $i++) {
		$send .= '<li>'
					.'<span class="name">N/A</span>'
					.'<span class="score">-</span>'
				.'</li>';
	}
	
	echo $send;
} else {
	echo 'ERREUR ! Pas de données envoyé !';
}