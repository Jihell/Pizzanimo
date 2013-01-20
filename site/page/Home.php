<div id="main">
	<h1>Pizzanimo</h1>
	<div id="game">
		<section id="limit"></section>
		<section id="gameOver">
			<h2>Perdu !</h2>
			<form action="highScore" method="post" class="sendScore">
				<h3>Enregistrer mon résultat</h3>
				<label>Pseudo</label>
				<input type="text" name="pseudo" value="<?php if(isset($_COOKIE['pseudo'])) { echo $_COOKIE['pseudo']; } ?>" />
				<input class="button" type="button" value="Enregistrer">
			</form>
			<p>Recommencer</p>
		</section>
		<section id="alert">
			<h2>Attendez la fin du mouvement !</h2>
		</section>
	</div><div id="score">
		<h2>Statistiques</h2>
		<ul class="stats">
			<li><label>Niveau</label><span id="pass">0</span></li>
			<li><label>Points</label><span id="points">0</span></li>
			<li><label>Meilleur combo</label><span id="combo">0</span></li>
			<li><label>Blocks détruits</label><span id="blocks">0</span></li>
			<li><label>Max de block d'un coup</label><span id="bestblocks">0</span></li>
			<li><label>Hauteur max</label><span id="maxHeight">5</span></li>
			<li><label>Hauteur</label><span id="height">5</span></li>
			<li><label>Coeficient de malus</label><span id="malus">1</span></li>
		</ul>
		<p>Recommencer</p>
		<h2>Meilleurs scores</h2>
		<nav>
			<ul>
				<li rel="points" class="selected">Points</li>
				<li rel="niveau">Niveau</li>
				<li rel="combo">Combo</li>
				<li rel="block">Blocks</li>
				<li rel="single">Max</li>
			</ul>
		</nav>
		<ol class="highscore">
			<?php 
			$res = \He\ORM::getDatabase('main')->getTable('highscore')->findBy(
				array(), 
				array('points' => 'DESC'), 
				array('begin' => 0, 'end' => 5)
			);

			$send = '';
			if(count($res) > 0) {
				foreach($res AS $row) {
					$send .= '<li>'
							.'<span class="name">'.$row->getPseudo().'</span>'
							.'<span class="score">'.$row->getPoints().'</span>'
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
			?>
		</ol>
	</div>
</div>
<div class="hidden" id="rules">
	<h2>Comment jouer ?</h2>
	<p>
		Pizzanimo est un puzzle game où vous devez assembler 3 formes identiques
		dans n'importe quel sens pour les faires disparaitres.
	</p>
	<p>
		Les blocks ainsi détruit laisse une place libre aux blocks supérieurs 
		qui viennent alors prendre leur place.
	</p>
	<p>
		Les block se déplacent en échangeant leurs places, cliquez sur 
		l'intersection de deux blocks pour les déplacer. Si une combinaison
		est valide, les blocks disparaissent.
	</p>
	<p>
		Les points sont calculés ainsi :<br/>
		10 * [nombre de blocks détruits] * [Nombre d'enchainement] * 
		[Nombre de packs de block détruits] * [Niveau] / [Coeficient de malus];
	</p>
	<p>
		La partie prend fin lorsque plus de 10 blocks sont empilés sur une colonne.
		Vous pouvez alors enregistrer votre score en mettant votre peudo.
	</p>
	<h2>Explication des statistiques</h2>
	<ul>
		<li>Niveau : Nombre d'échanges de blocks</li>
		<li>Points : Points gagnés</li>
		<li>Meilleur combo : Nombre d'enchainements maximum entre deux clicks</li>
		<li>Blocks détruits : Nombre de blocks détruits depuis le début de partie</li>
		<li>Max de blocks d'un coup : Nombre de blocks détruit en un seul enchainement</li>
		<li>Hauteur max : Hauteur maximum atteinte dans cette partie</li>
		<li>Hauteur : Hauteur actuelle de la plus haute pile de block</li>
		<li>Coeficient de malus : Nombre de fois que vos déplacement n'ont pas détruit de block. Le score est divisé par ce nombre.</li>
	</ul>
	<div class="closeOV">Fermer</div>
</div>
<div class="hidden" id="aPropos">
	<h2>A propos</h2>
	<p>
		Pizzanimo est entièrement écrit en javascript, en s'appuillant sur le 
		framework jQuery.
	</p>
	<p>
		Le code source du jeu (pjcGame.js) est entièrement libre de droit. 
		Vous être libre de le copier, diffuser, modifier comme bon vous semble.
	</p>
	<p>
		La partie serveur du jeu (enregistrement et affichage des meilleurs 
		scores) à été codé avec Hygrogen Engine (alpha), la version allégée 
		d'Helium Engine (aussi en alpha), mon projet de framework PHP.<br/>
		Plus d'info prochainement sur mon portfolio.
	</p>
	<h2>Remerciements</h2>
	<p>
		Merci à Aurélie pour m'avoir suggérer le développement de ce mini-jeu.
		Il est toujours instructif de tenter de réaliser quelque chose avec
		un language qui n'est pas forcément le plus optimal, ne serais-ce que
		d'un point de performances.
	</p>
	<p>
		Merci également à Aurélie et Garance pour leurs premiers retour ainsi
		que les sessions de test.
	</p>
	<p>
		Les dessins des animaux et icones sont issues de <a href="http://icones.pro/">icones.pro</a>. Merci pour leur service.
	</p>
	<h2>Plus d'information</h2>
	<p>
		Vous pouvez retrouver les autres création sur mon 
		<a href="http://www.joseph-lemoine.fr">portfolio</a>
	</p>
	<div class="closeOV">Fermer</div>
</div>