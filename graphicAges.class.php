<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class GraphicAgesException extends Exception{}
class BadSideException extends GraphicAgesException{}

/**
 * Description of graphicAges
 *
 * @author fabien.sanchez
 */
class GraphicAges {
	
	private $x_px;
	private $y_px;
	private $marge;
	private $max_data;
	
	private $serie_droite;
	private $serie_droite_libelle;
	private $serie_droite_couleur;
	private $serie_gauche;
	private $serie_gauche_libelle;
	private $serie_gauche_couleur;


	const DROITE = 0;
	const GAUCHE = 1;
	
	function __construct($x, $y) {
		$this->x_px = $x;
		$this->y_px = $y;
		$this->marge = 20;
		$this->max_data = 0;
	}
	
	function addSerie($side, $serie, $libelle="", $couleur=""){
		if( strtoupper($side) == "DROITE" ){
			$side = self::DROITE;
		}
		if( strtoupper($side) == "GAUCHE" ){
			$side = self::GAUCHE;
		}
		switch ($side) {
			case self::DROITE:
				$this->findMaxData($serie);
				$this->serie_droite = $serie;
				if($libelle==''){
					$libelle='DROITE';
				}
				if($couleur==''){
					$couleur='#8888ff';
				}
				$this->serie_droite_libelle = $libelle;
				$this->serie_droite_couleur = $couleur;
				break;			
			case self::GAUCHE:
				$this->serie_gauche = $serie;				
				if($libelle==''){
					$libelle='GAUCHE';
				}
				if($couleur==''){
					$couleur='#ff8888';
				}
				$this->serie_gauche_libelle = $libelle;
				$this->serie_gauche_couleur = $couleur;
				break;
			default:
				throw new BadSideException("'$side' is unknown");
		}
	}
	
	function generateIn($fileName) {
		$fh = fopen($fileName, 'w');
		$this->fwriteSVG($fh);
		fclose($fh);
	}
	
	private function fwriteSVG($handle) {
		fwrite($handle, '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">');
		fwrite($handle, "<svg width=\"{$this->x_px}px\" height=\"{$this->y_px}\">");
		$this->fwriteGraph($handle);
		fwrite($handle, '</svg>');
	}
	
	private function fwriteGraph($handle) {
		$this->fwriteRepere($handle);
	}
	
	private function fwriteFondGraph($handle) {
		fwrite($handle, '<g id="bg">');
		
		fwrite($handle, '</g>');
		
	}
	
	private function fwriteRepere($handle) {
		fwrite($handle, '<g id="repere">');
		
		fwrite($handle, '</g>');
	}
	
	private function fwriteData($handle) {
		fwrite($handle, '<g id="data">');
		
		fwrite($handle, '</g>');
	}	
	
	private function fwriteLibRepere($handle) {
		fwrite($handle, '<g id="libRep">');
		
		fwrite($handle, '</g>');
	}
	
	private function fwriteLibData($handle) {
		fwrite($handle, '<g id="libDat">');
		
		fwrite($handle, '</g>');
	}
	
	private function findMaxData($serie) {
	}
	
}
