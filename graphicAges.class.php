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
	private $milieu_px;
	private $marge;
	private $max_data;
	private $absysMax;
	private $fontSize;
	private $lineHeight;
	
	private $ordonnee;
	private $serie_droite;
	private $serie_droite_libelle;
	private $serie_droite_couleur;
	private $serie_gauche;
	private $serie_gauche_libelle;
	private $serie_gauche_couleur;

	const DROITE = 0;
	const GAUCHE = 1;
	
	function __construct($x, $y, $marge = 20, $fontSize = 10) {
		$this->x_px = $x;
		$this->milieu_px = (int) $x / 2;
		$this->y_px = $y;
		$this->marge = $marge;
		$this->max_data = 0;
		$this->fontSize = $fontSize;
		$this->lineHeight = (int) $fontSize * 1.5;
	}
	
	function setOrdonnee($ordonnee){
		
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
				$this->findMaxData($serie);
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
		
		// 1 -> 2
		// 9 -> 10
		// 11 -> 12
		// 100 -> 110
		$log = log10($this->max_data)-1;
		$this->absysMax = ceil($this->max_data / 10^$log) * 10^$log;
		
		$fh = fopen($fileName, 'w');
		$this->fwriteSVG($fh);
		fclose($fh);
	}
	
	private function fwriteSVG($handle) {
		fwrite($handle, '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">');
		fwrite($handle, "<svg width=\"{$this->x_px}px\" height=\"{$this->y_px}px\" xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" >");
		$x = $this->x_px - 1;
		$y = $this->y_px - 1;
		fwrite($handle, "<rect x=\"1\" y=\"1\" width=\"$x\" height=\"$y\" fill=\"#ffffff\" stroke=\"#000000\" />");
		$this->fwriteGraph($handle);
		fwrite($handle, '</svg>');
	}
	
	private function fwriteGraph($handle) {
		
		$this->fwriteFondGraph($handle);
		$this->fwriteData($handle);
		$this->fwriteRepere($handle);
		$this->fwriteLibRepere($handle);
		$this->fwriteLibData($handle);
		
	}
	
	private function fwriteFondGraph($handle) {
		fwrite($handle, '<g id="bg">');
		$yh = $this->marge;
		$yb = (int) $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x1min = $this->milieu_px - $this->marge;
		$x1max = $this->marge;
		$x2min = $this->milieu_px + $this->marge;
		$x2max = $this->x_px - $this->marge;
		for($x = $x1min; $x > $x1max; $x += ( $x1max - $x1min) / $this->absysMax){
			fwrite($handle, "<line x1=\"$x\" y1=\"$yb\" x2=\"$x\" y2=\"$yh\" ".
				"stroke=\"#808080\" stroke-width=\"1\" stroke-dasharray=\"3,5\" />");
		}
		for($x = $x2min; $x < $x2max; $x += ( $x2max - $x2min) / $this->absysMax){
			fwrite($handle, "<line x1=\"$x\" y1=\"$yb\" x2=\"$x\" y2=\"$yh\" ".
				"stroke=\"#808080\" stroke-width=\"1\" stroke-dasharray=\"3,5\" />");
		}
		fwrite($handle, '</g>');
		
	}
	
	private function fwriteRepere($handle) {
		fwrite($handle, '<g id="repere">');
		
		$x1 = $this->marge;
		$y1 = $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x2 = $this->milieu_px - $this->marge;
		$y2 = $y1;
		$x3 = $x2;
		$y3 = $this->marge;
		fwrite($handle,"<polyline fill=\"none\" stroke=\"#000000\" stroke-width=\"3\" points=\"$x1,$y1 $x2,$y2 $x3,$y3\" />");
		
		$x1 = $this->x_px - $this->marge;
		$y1 = $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x2 = $this->milieu_px + $this->marge;
		$y2 = $y1;
		$x3 = $x2;
		$y3 = $this->marge;
		fwrite($handle,"<polyline fill=\"none\" stroke=\"#000000\" stroke-width=\"3\" points=\"$x1,$y1 $x2,$y2 $x3,$y3\" />");
		
		fwrite($handle, '</g>');
	}
	
	private function fwriteData($handle) {
		fwrite($handle, '<g id="data">');
		
		$yh = $this->marge;
		$yb = (int) $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x1min = $this->milieu_px - $this->marge;
		$x1max = $this->marge;
		$x2min = $this->milieu_px + $this->marge;
		$x2max = $this->x_px - $this->marge;
		$step = ( $yb - $yh ) / count($this->ordonnee);
		
		for ($i = 0; $i < count($this->ordonnee); $i++){
			$l1 = (int) (( $x1min - $x1max ) / $this->absysMax ) * $this->serie_gauche;
			$h = (int) $step - 4;
			$x1 = 0;
			fwrite($handle,"<rect x=  \"400\" y=\"100\" width=\"400\" height=\"200\" fill=\"yellow\" stroke=\"navy\" stroke-width=\"10\" />");
			fwrite($handle,"<rect x=  \"400\" y=\"100\" width=\"400\" height=\"200\" fill=\"yellow\" stroke=\"navy\" stroke-width=\"10\" />");
		}
		
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
		$values = array_values($serie);
		$maxVal = max($values);
		$this->max_data = max([
				$maxVal, 
				$this->max_data
			]);
	}
		
}
