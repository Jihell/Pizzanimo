<?php
/**
 * Grid
 *
 * @author Joseph Lemoine - lemoine.joseph@gmail.com
 * @version 1
 */

class Grid
{
	private $_grid = array();
	private $_validation = array();
	
	private $_scanPack			= 0;
	private $_pass				= 0;
	private $_points			= 0;
	private $_currentCombo		= 0;
	private $_combo				= 0;
	private $_currentBlocks		= 0;
	private $_bestBlocks		= 0;
	private $_blocks			= 0;
	private $_maxHeight			= 5;
	private $_badMove			= 0;
	private $_param = array(
		'width'			=>	10, // largeur du tableau
		'diffent'		=>	3, // Nomdre de type d'éléments différents à l'initialisation
		'maxDiffent'	=> 	5, // Nomdre maximum de type d'éléments
		'initialHeight' =>	5, // Nombre de ligne présente initialement
		'blocks'		=>	3, // Nomdre d'élément adjacent pour detruire les block
		'progress'		=>	10, // NB de passes avant d'ajouter un block d'un nouveau type
		'gameOver'		=>	10 // Taille limite du tableau avant game over
	);
	
	public function init()
	{
		;
	}
	
	public function makeRow($row)
	{
		$this->_grid[$row] = array(); // Initialisation de la grille
		$i = 0; // Curseur en X
		$max_iteration = $this->_param['blocks'] * $this->_param['width'] * 2; // Nombre de tentative de création de cellule
		$nb_iteration = 0; // Nombre de tentative de création de cellule

		/**
		 * Traitement
		 */
		while($i < $this->_param['width'] && $nb_iteration < $max_iteration) {
			// On créer une nouvelle valeur entre 1 et x
			$maxDev = $this->_param['diffent'] + floor($row / $this->_param['progress']);
			if($this->_param['diffent'] + floor($row / $this->_param['progress']) > $this->_param['maxDiffent']) {
				$maxDev = pjcMeth.params.maxDiffent;
			}

			$this->_grid[$row][$i] = rand(1, $maxDev);

			// On vérifie que cette valeur le valide pas 3 cellules
			$this->_initValidation(); // Initialisation du validateur
			if($this->countCloser($row_number, $i, $this->_grid[$row][$i]) < $this->_param['blocks']) {
				// On peu ajouter cette valeur à la ligne
				$i++;
			}

			// anti boucle infinie (rand inside)
			$nb_iteration++;
		}

		if($nb_iteration >= $max_iteration) {
			return 0;
		} else {
			return ++$row_number;
		}
	}
	
	private function _countCloser($corx, $cory, $value)
	{
		$this->_validation[$corx][$cory] = true;

		$send = 1;
		if(typeof validation[$corx-1] != 'undefined') {
			send += (!validation[corx-1][cory] ? pjcMeth.countCloser(corx-1, cory, value) : 0);
		}
		if(typeof validation[corx+1] != 'undefined') {
			send += (!validation[corx+1][cory] ? pjcMeth.countCloser(corx+1, cory, value) : 0);
		}

		send += (!validation[corx][cory-1] ? pjcMeth.countCloser(corx, cory-1, value) : 0);
		send += (!validation[corx][cory+1] ? pjcMeth.countCloser(corx, cory+1, value) : 0);

		return send;

		// Fin de process, aucun succès, 0 similitudes
		return 0;
	}
	
	private function initValidation()
	{
		$this->_validation = array();
	}
}