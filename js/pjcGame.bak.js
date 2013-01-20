/**
 * Plugin Pizzanimo
 *
 * @author lemoine.joseph@gmail.com
 * @version 1
 */

(function($) {
	var grid = [];
	var validation = [];
	
	/**
	 * pjcGame
	 */
	var pjcMeth = {
		/**********************************************************************
		 * Tableau des options du jeu.
		 **********************************************************************
		 */
		params: {
			width:			10, // largeur du tableau
			diffent:		3, // Nomdre de type d'éléments différents à l'initialisation
			maxDiffent:		5, // Nomdre maximum de type d'éléments
			initialHeight:	5, // Nombre de ligne présente initialement
			blocks:			3, // Nomdre d'élément adjacent pour detruire les block
			progress:		10, // NB de passes avant d'ajouter un block d'un nouveau type
			gameOver:		10 // Taille limite du tableau avant game over
		},
		/**********************************************************************
		 * On créer 5 lignes avec des class de couleur1 à couleurN
		 * 
		 * @return void
		 **********************************************************************
		 */
		init : function() {
			grid = [];
			$('#game div').remove();
			pjcMeth.scanPack			= 0;
			pjcMeth.pass				= 0;
			pjcMeth.points				= 0;
			pjcMeth.currentCombo		= 0;
			pjcMeth.combo				= 0;
			pjcMeth.currentBlocks		= 0;
			pjcMeth.bestBlocks			= 0;
			pjcMeth.blocks				= 0;
			pjcMeth.maxHeight			= 5;
			pjcMeth.badMove				= 0;
			
			pjcMeth.pass = 0;
			while(pjcMeth.pass < pjcMeth.params.initialHeight) {
				pjcMeth.pass = pjcMeth.makeRow(pjcMeth.pass);
			}
			
			pjcMeth.height = pjcMeth.pass;
			
			pjcMeth.writeStat();
		},
		/**********************************************************************
		 * Affiche la grille
		 * 
		 * @param el DOMElement
		 * @return void
		 **********************************************************************
		 */
		makeGrid: function(el) {
			var width = el.width();
			var height = el.height();

			// Affichage de la grille
			for(i = 0; i < grid.length; i++) {
				for(j = 0; j < grid[i].length; j++) {
					var newDiv = $('<div></div>', {
						'rel': i+'x'+j,
						'style': 'top:'+(height-pjcMeth.pass*48+i*48)+'px; left:'+(j*48+((width-pjcMeth.params.width*48)/ 2))+'px;',
						'class': 'back color'+grid[i][j]
					});
					
					el.append(newDiv);
				}
			}
		},
		/**********************************************************************
		 * Crée une ligne suplémentaire dans la variable grid à la position
		 * row_number
		 * 
		 * @param row_number integer
		 * @return integer
		 **********************************************************************
		 */
		makeRow: function(row_number) {
			/**
			 * Initialisations
			 */
			grid[row_number] = []; // Initialisation de la grille
			var i = 0; // Curseur en X
			var max_iteration = pjcMeth.params.blocks * pjcMeth.params.width * 2; // Nombre de tentative de création de cellule
			var nb_iteration = 0; // Nombre de tentative de création de cellule

			/**
			 * Traitement
			 */
			while(i < pjcMeth.params.width && nb_iteration < max_iteration) {
				// On créer une nouvelle valeur entre 1 et x
				var maxDev = pjcMeth.params.diffent+Math.floor(row_number / pjcMeth.params.progress);
				if(pjcMeth.params.diffent+Math.floor(row_number/pjcMeth.params.progress) > pjcMeth.params.maxDiffent) {
					maxDev = pjcMeth.params.maxDiffent;
				}
				
				grid[row_number][i] = Math.ceil(Math.random()*(maxDev));
				
				// On vérifie que cette valeur le valide pas 3 cellules
				pjcMeth.initValidation(grid); // Initialisation du validateur
				if(pjcMeth.countCloser(row_number, i, grid[row_number][i]) < pjcMeth.params.blocks) {
					// On peu ajouter cette valeur à la ligne
					i++;
				}
				
				// anti boucle infinie (rand inside)
				nb_iteration++;
			}
			
			if(nb_iteration >= max_iteration) {
				return 0;
			} else {
				return ++row_number;
			}
		},
		/**********************************************************************
		 * Compte le nombre de case similaire depuis la case x y.
		 * Les cases sont marqués dans le tableau validation afin d'éviter les
		 * récurtions infinies
		 * 
		 * @param corx integer
		 * @param cory integer
		 * @param value integer
		 * @return integer
		 ***********************************************************************
		 */
		countCloser: function(corx, cory, value) {
			// Si le type est valide, on scan
			if(typeof grid[corx] != 'undefined' && value > 0) {
				if(typeof grid[corx][cory] != 'undefined') {
					// Si la valeur est identique, on ajoute un, et on scan les adjacents en récursif
					if(grid[corx][cory] == value) {
						validation[corx][cory] = true;
						
						var send = 1;
						if(typeof validation[corx-1] != 'undefined') {
							send += (!validation[corx-1][cory] ? pjcMeth.countCloser(corx-1, cory, value) : 0);
						}
						if(typeof validation[corx+1] != 'undefined') {
							send += (!validation[corx+1][cory] ? pjcMeth.countCloser(corx+1, cory, value) : 0);
						}
						
						send += (!validation[corx][cory-1] ? pjcMeth.countCloser(corx, cory-1, value) : 0);
						send += (!validation[corx][cory+1] ? pjcMeth.countCloser(corx, cory+1, value) : 0);
						
						return send;
					}
				}
			}
			
			// Fin de process, aucun succès, 0 similitudes
			return 0;
		},
		/***********************************************************************
		 * Ajoute une class aux div qui devront être effacés et transforme
		 * les valeurs de la grille à 0
		 * 
		 * @return void
		 ***********************************************************************
		 */
		markCloser: function() {
			var destroyed = 0;
			for(var i = 0; i < validation.length; i++) {
				for(var j = 0; j < validation[i].length; j++) {
					if(validation[i][j]) {
						$('[rel="'+i+'x'+j+'"]').addClass('flag');
						grid[i][j] = 0;
						destroyed++;
					}
				}
			}
			
			return destroyed;
		},
		/***********************************************************************
		 * Initialisation du tableau de validation
		 * 
		 * @return void
		 ***********************************************************************
		 */
		initValidation: function(grid) {
			validation = [];
			for(var i = 0; i < grid.length; i++) {
				validation[i] = [];
				for(var j = 0; j < pjcMeth.params.width; j++) {
					validation[i][j] = false;
				}
			}
		},
		/***********************************************************************
		 * Détecte la diretion vers laquel pointe le curseur de la souris.
		 * 
		 * @param div DOMElement
		 * @param e Event
		 * @return JSON Direction en x et y
		 ***********************************************************************
		 */
		getDirection: function(div, e) {
			var height = div.height();
			var width = div.width();
			
			var relHeight = e.pageY-div.offset().top;
			var relWidth = e.pageX-div.offset().left;
			
			var XDir = (relWidth < width / 2 ? {x: -1, y: 0} : {x: 1, y: 0});
			var YDir = (relHeight < height / 2 ? {x: 0, y: -1} : {x: 0, y: 1});
			
			var absoluteX = (relWidth < width / 2 ? relWidth : width - relWidth);
			var absoluteY = (relHeight < height / 2 ? relHeight : height - relHeight);
			
			return absoluteX < absoluteY ? XDir : YDir;
		},
		/***********************************************************************
		 * Déplace deux block adjacent, selon la direction définie par la
		 * méthode getDirection.
		 * 
		 * Les deux block ainsi déplacés change d'identifiant pour les prochains
		 * déplacements
		 * 
		 * @param name_this string nom de l'identifiant du trigger
		 * @param name_other string nom de la div adjacente concernée
		 * @param callback function() Fonction a exéctuter en fin de mouvement
		 * @return void
		 ***********************************************************************
		 */
		moveBlock: function(name_this, name_other, callback) {
			// Récupération des positions
			var posi = {
				iniTop: $('.back[rel="'+name_this+'"]').css('top'),
				iniLeft: $('.back[rel="'+name_this+'"]').css('left'),
				adjTop: $('.back[rel="'+name_other+'"]').css('top'),
				adjLeft: $('.back[rel="'+name_other+'"]').css('left')
			};

			// Déplacement
			$('.back[rel="'+name_this+'"]').animate({
				top: posi.adjTop,
				left: posi.adjLeft
			}, 200, function(){$(this).attr('rel', name_other);});
			$('.back[rel="'+name_other+'"]').animate({
				top: posi.iniTop,
				left: posi.iniLeft
			}, 200, function(){
				$(this).attr('rel', name_this);
				if(typeof callback != 'undefined') {
					callback();
				}
			});
		},
		/***********************************************************************
		 * Inverse deux positions sur la grille, selon la direction founie
		 * 
		 * @param direction JSON
		 * @param cord array position initiale en x (1) et y (0)
		 * @return void
		 ***********************************************************************
		 */
		moveGrid: function(direction, cord) {
			// Ajustement de la grille
			var cords = {
				iniX: parseInt(cord[1]),
				iniY: parseInt(cord[0]),
				adjX: direction.x+parseInt(cord[1]),
				adjY: direction.y+parseInt(cord[0])
			};
			
			var tmp = grid[cords.iniY][cords.iniX];
			grid[cords.iniY][cords.iniX] = grid[cords.adjY][cords.adjX];
			grid[cords.adjY][cords.adjX] = tmp;
		},
		/***********************************************************************
		 * Scan la grille et marque les div à détruire.
		 * Si des div on été détruites, on réajuste la grille et on relance
		 * la méthode jusqu'à ce qu'aun block ne soit détruit.
		 * 
		 * @return nombre de pack scannés
		 ***********************************************************************
		 */
		scanGroup: function() {
			// On stop les animations résiduelles
			$('#game div').stop(true, true);
			
			// Initialisation du nombre de blocks détruits
			var destroyed = 0;
			
			// Recherche des block par pack de pjcMeth.params.blocks (3 de base)
			for(var i = 0; i < grid.length; i++) {
				for(var j = 0; j < grid[i].length; j++) {
					pjcMeth.initValidation(grid); // Initialisation du validateur
					if(pjcMeth.countCloser(i, j, grid[i][j]) >= pjcMeth.params.blocks) {
						// On a trouvé un pack de plus !
						pjcMeth.scanPack++; 
						// Marquage des div à effacer
						destroyed += pjcMeth.markCloser(); 
					}
				}
			}
			
			// On relance la méthode si on a détruit des blocks
			if(destroyed > 0) {
				// Incrémentation du compteur de passe des destruction
				pjcMeth.currentCombo++;
				// Incrémentation du comteur de blocks détruits
				pjcMeth.currentBlocks += destroyed;
				// Régénération de la grille (on fait "tomber" les blocks)
				pjcMeth.rebuildGrid();
				
				// Récursion avec le temps du mouvement compris
				setTimeout(function(){pjcMeth.scanGroup();}, 500);
			// Sinon on ajoute une ligne et on écrit le score
			} else {
				// Ajout des statistiques
				pjcMeth.writeStat();
				
				// Ajout d'une ligne
				pjcMeth.makeRow(pjcMeth.pass);
				for(j = 0; j < grid[pjcMeth.pass].length; j++) {
					
					var newDiv = $('<div></div>', {
						'rel': pjcMeth.pass+'x'+j,
						'style': 'top:'+$('#game').height()+'px; left:'+(j*48+(($('#game').width()-pjcMeth.params.width*48)/ 2))+'px;',
						'class': 'back color'+grid[pjcMeth.pass][j]
					});
					
					$('#game').append(newDiv);
				}
				pjcMeth.pass++;
				
				// Déplacement du tableau vers le haut
				$('#game > div').each(function(){
					var moove = parseInt($(this).css('top').replace('px', ''))-48;
					$(this).stop(true, true).animate({
						top: moove+'px'
					}, 200);
				});
				
				// Condition du game over
				if(pjcMeth.height > pjcMeth.params.gameOver) {
					pjcMeth.canUploadScore = true;
					$('#gameOver').fadeIn(200);
				// Si on est à la hauteur max avant le game over, feedback
				} else if(pjcMeth.height == pjcMeth.params.gameOver) {
					$('#game').effect("highlight", 'fast');
				}
				
				pjcMeth.canMove = true;
			}
			
			return pjcMeth.scanPack;
		},
		/***********************************************************************
		 * Régénération de la grille pour faire "tomber" les block dans les 
		 * espaces vides.
		 * 
		 * C'est ici qu'on supprime les div avec la class '.flag' et qu'on 
		 * déplace celles marqués .moover
		 * 
		 * @return void
		 ***********************************************************************
		 */
		rebuildGrid: function(){
			// Tableau des blocks à bouger
			var blocks = [];
			
			// Ajustement des blocks depuis le bas
			for(var i = grid.length-1; i >= 0; i--) {
				blocks[i] = [];
				for(var j = 0; j < grid[i].length; j++) {
					blocks[i][j] = {name: i+'x'+j, cord: {x: i, y: j}};
					
					// Si on est sur une case vide
					if(grid[i][j] == 0) {
						blocks[i][j] = pjcMeth.catchBlockUpper(i, j);
					}
				}
			}
			
			// Suppression de toute les div marqués avec flag
			$('.flag').each(function() {
				$(this).css({background: '#f00'});
				$(this).fadeOut(200, function(){
                        $(this).remove();
                    });
			});
			// Déplacement des div qui "tombent"
			$('.moover').each(function() {
				$(this).stop(true, true).animate({top: $(this).attr('title')}, 500, function() {
					$(this).attr('rel', $(this).attr('name'));
					$(this).removeAttr('name');
					$(this).removeAttr('title');
					$(this).removeClass('moover');
				});
			});
		},
		/***********************************************************************
		 * Récupération de la div la plus proche du "trou" actuel.
		 * La div en question est marqué pour un futur déplacement, et la case
		 * de la grille est transformée.
		 * 
		 * @param row integer n° de ligne actuel
		 * @param column integer Colonne concernée par le scan
		 * @return JSON
		 ***********************************************************************
		 */
		catchBlockUpper: function(row, column) {
			// On attrape le block le plus haut
			for(var i = row; i >= 0; i--) {
				if(grid[i][column] > 0) {
					// Modification de la grille
					grid[row][column] = grid[i][column];
					grid[i][column] = 0;
					
					$('[rel="'+i+'x'+column+'"]').addClass('moover');
					$('[rel="'+i+'x'+column+'"]').attr('title', $('[rel="'+row+'x'+column+'"]').css('top'));
					$('[rel="'+i+'x'+column+'"]').attr('name', row+'x'+column);
					
					// Renvoi de la valeur pour le déplacement de la div
					return {name: i+'x'+column, y: i, x: column, target: row+'x'+column};
				}
			}
			
			// On ne bouge pas
			return {y: row, x: column};
		},
		/***********************************************************************
		 * Création des statistiques
		 * 
		 * @return void
		 ***********************************************************************
		 */
		writeStat: function() {
			pjcMeth.calculateHeight();
			
			pjcMeth.blocks += pjcMeth.currentBlocks;
			if(pjcMeth.currentBlocks > pjcMeth.bestBlocks) {
				pjcMeth.bestBlocks = pjcMeth.currentBlocks;
			}
			
			if(pjcMeth.currentCombo > pjcMeth.combo) {
				pjcMeth.combo = pjcMeth.currentCombo;
			}
			
			if(pjcMeth.scanPack == 0) {pjcMeth.badMove++;}
			
			pjcMeth.points += parseInt(10*pjcMeth.currentBlocks*pjcMeth.currentCombo*pjcMeth.scanPack/pjcMeth.badMove*(pjcMeth.pass-4));
//			pjcMeth.points += parseInt(10*pjcMeth.currentBlocks*pjcMeth.currentCombo*pjcMeth.scanPack*(pjcMeth.pass-4));
			
			$('#pass').html(pjcMeth.pass-4);
			$('#points').html(pjcMeth.points);
			$('#combo').html(pjcMeth.combo);
			$('#blocks').html(pjcMeth.blocks);
			$('#bestblocks').html(pjcMeth.bestBlocks);
			$('#maxHeight').html(pjcMeth.maxHeight);
			$('#height').html(pjcMeth.height);
			$('#malus').html(pjcMeth.badMove);
			
			pjcMeth.currentBlocks = 0;
			pjcMeth.currentCombo = 0;
			pjcMeth.scanPack = 0;
		},
		/***********************************************************************
		 * Calcul de la hauteur maximum actuelle. Indispensable pour détecter
		 * le game over
		 * 
		 * @return integer
		 ***********************************************************************
		 */
		calculateHeight: function() {
			pjcMeth.height = 0;
			
			for(var j = 0; j < pjcMeth.params.width; j++) {
				var i = grid.length - 1;
				
				var currentHeight = 0;
				var notFoud = true;
				
				while(i >= 0 && notFoud) {
					currentHeight++;
					if(grid[i][j] == 0) {
						notFoud = false;
					}
					i--;
				}
				
				if(notFoud) {currentHeight++;}
				if(pjcMeth.pass == pjcMeth.params.initialHeight) {currentHeight--;}
				
				// Hauteur actuelle
				if(currentHeight > pjcMeth.height) {
					pjcMeth.height = currentHeight;
				}
			}	
			
			// Hauteur maximum atteinte
			if(pjcMeth.height > pjcMeth.maxHeight) {
				pjcMeth.maxHeight = pjcMeth.height;
			}
			
			return pjcMeth.height;
		},
		/***********************************************************************
		 * Récupère les différents highscore selon le menu choisi
		 ***********************************************************************
		 */
		refreshHighScore: function() {
			$.post('/ajax.php?file=showHighscore', {menu: $('nav li.selected').attr('rel')}, function(msg){
				$('.highscore').html(msg);
			});
		},
		/***********************************************************************
		 * Propriétés du plugin
		 ***********************************************************************
		 */
		canMove: true,
		canUploadScore: false,
		scanPack: 0, // Identifiant des packets détectés
		pass: 0,
		points: 0,
		currentCombo: 0,
		combo: 0,
		currentBlocks: 0,
		bestBlocks: 0,
		blocks: 0,
		maxHeight: 5,
		height: 5,
		badMove: 0
	};
	
	/**
	 * ========================================================================
	 *						pjcGame - Initialisation
	 * ========================================================================
	 */
	$.fn.pjcGame = function(params) {
		
		this.pjcTriggers();
		
		pjcMeth.params = $.extend(pjcMeth.params, params);
		
		// Initialisation, on créer la grille
		pjcMeth.init(params);
		
		pjcMeth.makeGrid(this);

		return this;
	};
	
	/**
	 * ========================================================================
	 *						pjcTriggers, CONTROLEUR
	 * 
	 * -> Ajout des évènements au sujet :
	 *		-> Hightlight au hover des objets alternables
	 *		-> Déplacement et destruction des blocks correspondant aux critères
	 *			-> Ajout de la ligne suplémentaire
	 * ========================================================================
	 */
	$.fn.pjcTriggers = function() {
		/**
		 **********************************************************************
		 * Inverse les block sélectionnés et déclenche les destructions
		 **********************************************************************
		 */
		$('#game').on('click', '.back', function(e) {
			// On déplace si aucun autre n'est en mouvement
			if(pjcMeth.canMove) {
				var direction = pjcMeth.getDirection($(this), e);
				var cord = $(this).attr('rel').split('x');

				var name_this = cord[0]+'x'+cord[1];
				var name_other = (direction.y+parseInt(cord[0]))+'x'+(direction.x+parseInt(cord[1]));

				// Seuleument si on a bien une div adjacente à cette position
				if(direction.y+parseInt(cord[0]) >= 0 && direction.y+parseInt(cord[0]) < grid.length &&
					direction.x+parseInt(cord[1]) >= 0 && direction.x+parseInt(cord[1]) < pjcMeth.params.width &&
					$('[rel="'+name_other+'"]').length > 0)
				{
					$('div.back').stop(true,true).removeClass('highlight');
					// On bloque les mouvement car il y en a déjà un en cours
					pjcMeth.canMove = false;

					// Déplacement dans la grille
					pjcMeth.moveGrid(direction, cord);
					// Déplacement des blocks
					pjcMeth.moveBlock(name_this, name_other, function(){
						// Destruction des combinaisons
						pjcMeth.scanGroup();
					});
				}
			} else {
				$('#alert').fadeIn(200, function(){$(this).fadeOut(200);});
			}
		}).on('mousemove', '.back', function(e){
			if(pjcMeth.canMove) {				
				var direction = pjcMeth.getDirection($(this), e);
				var cord = $(this).attr('rel').split('x');

				var name_other = (direction.y+parseInt(cord[0]))+'x'+(direction.x+parseInt(cord[1]));

				$('#game div.back').removeClass('highlight');
				// Seuleument si on a bien une div adjacente à cette position
				if(typeof grid[direction.y+parseInt(cord[0])] != 'undefined') {
					if(typeof grid[direction.y+parseInt(cord[0])][direction.x+parseInt(cord[1])] != 'undefined') {
						if(grid[direction.y+parseInt(cord[0])][direction.x+parseInt(cord[1])] > 0) {
							// On highlight lui même
							$(this).addClass('highlight');
							// On highlight dans la direction
							$('.back[rel="'+name_other+'"]').addClass('highlight');
						}
					}
				}
			}
		}).on('mouseout', '.back', function(){
			$('#game div.back').removeClass('highlight');
		});
		/**
		 **********************************************************************
		 * Recommencer le niveau
		 **********************************************************************
		 */
		$(document).on('click', '#gameOver p, #score p', function() {
			$('*').stop(true, true);
			pjcMeth.init();
			pjcMeth.makeGrid($('#game'));
			$('#gameOver').fadeOut(200, function() {
				$('.sendScore').show();
			});
		});
		
		/**
		 **********************************************************************
		 * Envoyer les high scores
		 **********************************************************************
		 */
		$(document).on('click', '#gameOver form .button', function(){
			if(pjcMeth.canUploadScore) {
				pjcMeth.canUploadScore = false;
				
				// On créer un cookie pour le pseudo
				setCookie('pseudo', $('.sendScore input[name="pseudo"]').val(), 300);
				
				$.post('/ajax.php?file=storHighscore', {
					pseudo: $('.sendScore input[name="pseudo"]').val(),
					niveau: pjcMeth.pass,
					points: pjcMeth.points,
					combo: pjcMeth.combo,
					block: pjcMeth.blocks,
					single: pjcMeth.bestBlocks
				}, function(msg){
					if(msg.match(/Erreur/)) {
						alert(msg);
					} else {
						openOverlay('<h2>'+msg+'</h2>'
							+'<input class="closeOV" type="button" value="Continuer ..."/>');
						
						$('.sendScore').slideUp(200);

						// Refresh des high score
						pjcMeth.refreshHighScore();
					}
				});
			}
		});
		
		/**
		 **********************************************************************
		 * Chargement des highscores
		 **********************************************************************
		 */
		$(document).on('click', '#score nav ul li', function() {
			$('#score nav ul li').removeClass('selected');
			$(this).addClass('selected');
			
			// Refresh des high score
			pjcMeth.refreshHighScore();
		});
		
		// On ne brise pas la chaine !
		return this;
	};
	
})(jQuery);

$(document).ready(function(){
	$('#game').pjcGame();
});