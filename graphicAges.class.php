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
	private $serie_droite_couleur_light;
	private $serie_gauche;
	private $serie_gauche_libelle;
	private $serie_gauche_couleur;
	private $serie_gauche_couleur_light;

	const DROITE = 0;
	const GAUCHE = 1;
	
	function __construct($x, $y, $marge = 20, $fontSize = 12) {
		$this->x_px = $x;
		$this->milieu_px = (int) $x / 2;
		$this->y_px = $y;
		$this->marge = $marge;
		$this->max_data = 0;
		$this->fontSize = $fontSize;
		$this->lineHeight = (int) $fontSize * 1;
		$this->creno = 0.9;
	}
	
	function setOrdonnee($ordonnee){
		$this->ordonnee = $ordonnee;
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
				$this->serie_droite_couleur_light = $this->melange($couleur, '#ffffff', .5);
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
				$this->serie_gauche_couleur_light = $this->melange($couleur, '#ffffff', .5);
				break;
			default:
				throw new BadSideException("'$side' is unknown");
		}
	}
	
	function generateIn($fileName) {
		
		$log = floor(log10($this->max_data)) - 1;
		$this->absysMax = (( ceil( $this->max_data ) / pow( 10, $log )) + 1 ) * pow( 10, $log );
		
		$fh = fopen($fileName, 'w');
		$this->fwriteSVG($fh);
		fclose($fh);
	}
	
	private function fwriteSVG($handle) {
		fwrite($handle, '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" '
				. '"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">');
		fwrite($handle, "<svg width=\"{$this->x_px}px\" height=\"{$this->y_px}px\" "
		. "xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\" >");
		$x = $this->x_px - 1;
		$y = $this->y_px - 1;
		fwrite($handle, "<rect x=\"1\" y=\"1\" width=\"$x\" height=\"$y\" "
				. "fill=\"#ffffff\" stroke=\"#000000\" />");
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
				"stroke=\"#bbbbbb\" stroke-width=\"1\" stroke-dasharray=\"3,5\" />");
		}
		for($x = $x2min; $x < $x2max; $x += ( $x2max - $x2min) / $this->absysMax){
			fwrite($handle, "<line x1=\"$x\" y1=\"$yb\" x2=\"$x\" y2=\"$yh\" ".
				"stroke=\"#bbbbbb\" stroke-width=\"1\" stroke-dasharray=\"3,5\" />");
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
		fwrite($handle,"<polyline fill=\"none\" stroke=\"#000000\" stroke-width=\"3\" "
				. "points=\"$x1,$y1 $x2,$y2 $x3,$y3\" />");
		
		$x1 = $this->x_px - $this->marge;
		$y1 = $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x2 = $this->milieu_px + $this->marge;
		$y2 = $y1;
		$x3 = $x2;
		$y3 = $this->marge;
		fwrite($handle,"<polyline fill=\"none\" stroke=\"#000000\" stroke-width=\"3\" "
				. "points=\"$x1,$y1 $x2,$y2 $x3,$y3\" />");
		
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
			$l1 = (int) (( $x1min - $x1max ) / $this->absysMax ) * $this->serie_gauche[$i];
			$l2 = (int) (( $x2max - $x2min ) / $this->absysMax ) * $this->serie_droite[$i];
			$h = (int) $step * $this->creno;
			$x1 = $x1min - $l1;
			$y = (int) $yb - ( $i * $step ) - ($step * (( 1 - $this->creno )/ 2 )) - $h;
			fwrite($handle,"<rect x=\"$x1\" y=\"$y\" width=\"$l1\" height=\"$h\" "
					. "fill=\"{$this->serie_gauche_couleur_light}\" stroke=\"{$this->serie_gauche_couleur}\"  "
					. "stroke-width=\"1\" />");
			fwrite($handle,"<rect x=\"$x2min\" y=\"$y\" width=\"$l2\" height=\"$h\" "
					. "fill=\"{$this->serie_droite_couleur_light}\" stroke=\"{$this->serie_droite_couleur}\" "
					. "stroke-width=\"1\" />");
		}
		
		fwrite($handle, '</g>');
	}	
	
	private function fwriteLibRepere($handle) {
		fwrite($handle, '<g id="libRep">');
		
		$yh = $this->marge;
		$yb = (int) $this->y_px - $this->marge -  2 * $this->lineHeight;
		$step = ( $yb - $yh ) / count($this->ordonnee);
		$h = (int) $step - 10;
		$x = $this->milieu_px;
		$l = (int) $this->marge * 2 - 10;
		$milieuG = (int)$this->milieu_px / 2;
		$milieuD = (int)$this->milieu_px * 3 / 2;
		$yLib =  (int) $this->y_px - $this->marge;
		for ($i = 0; $i < count($this->ordonnee); $i++){
			$y = (int) $yb - ( $i * $step ) - ( $step / 2 ) + ( $this->lineHeight / 2 );
			fwrite($handle,"<text x=\"$x\" y=\"$y\" textLength=\"$l\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:bold;"
					. "text-align:center;line-height:{$this->lineHeight};text-anchor:middle;font-family:Arial\" >"
					. "{$this->ordonnee[$i]}</text>");
		}
		fwrite($handle,"<text x=\"$milieuD\" y=\"$yLib\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:bold;"
					. "text-align:center;line-height:{$this->lineHeight};text-anchor:middle;font-family:Arial\" >"
					. "{$this->serie_droite_libelle}</text>");
		fwrite($handle,"<text x=\"$milieuG\" y=\"$yLib\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:bold;"
					. "text-align:center;line-height:{$this->lineHeight};text-anchor:middle;font-family:Arial\" >"
					. "{$this->serie_gauche_libelle}</text>");
		fwrite($handle, '</g>');
	}
	
	private function fwriteLibData($handle) {
		fwrite($handle, '<g id="libDat">');
		$yh = $this->marge;
		$yb = (int) $this->y_px - $this->marge -  2 * $this->lineHeight;
		$x1min = $this->milieu_px - $this->marge;
		$x1max = $this->marge;
		$x2min = $this->milieu_px + $this->marge;
		$x2max = $this->x_px - $this->marge;
		$step = ( $yb - $yh ) / count($this->ordonnee);
		$milieuG = (int)$this->milieu_px/2;
		$milieuD = (int)$this->milieu_px*3/2;
		
		for ($i = 0; $i < count($this->ordonnee); $i++){
			$l1 = (int) $this->milieu_px - $this->marge - (( $x1min - $x1max ) / $this->absysMax ) * $this->serie_gauche[$i];
			$l2 = (int) $this->milieu_px + $this->marge + (( $x2max - $x2min ) / $this->absysMax ) * $this->serie_droite[$i];			
			$y = (int) $yb - ( $i * $step ) - (( $step - $this->lineHeight )/ 2 );
			if ($l1 > $milieuG) {
				$l1 -= 10;
				fwrite($handle,"<text x=\"$l1\" y=\"$y\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:normal;fill:{$this->serie_gauche_couleur};"
					. "text-align:end;line-height:{$this->lineHeight};text-anchor:end;font-family:Arial\" >"
					. "{$this->serie_gauche[$i]}</text>");
			}else{
				$l1 += 10;
				fwrite($handle,"<text x=\"$l1\" y=\"$y\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:normal;fill:#ffffff;"
					. "line-height:{$this->lineHeight};font-family:Arial\" >"
					. "{$this->serie_gauche[$i]}</text>");
			}
			if ($l2 > $milieuD) {
				$l2 -= 10;
				fwrite($handle,"<text x=\"$l2\" y=\"$y\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:normal;fill:#ffffff;"
					. "text-align:end;line-height:{$this->lineHeight};text-anchor:end;font-family:Arial\" >"
					. "{$this->serie_droite[$i]}</text>");
			}else{
				$l2 += 10;
				fwrite($handle,"<text x=\"$l2\" y=\"$y\" "
					. "style=\"font-size:{$this->fontSize}px;font-style:normal;font-weight:normal;fill:{$this->serie_droite_couleur};"
					. "line-height:{$this->lineHeight};font-family:Arial\" >"
					. "{$this->serie_droite[$i]}</text>");
			}
		}
		
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
	
	private function melange($couleur0, $couleur1, $proucentage) {
		$couleurOut = array();
		$coul0 = $this->colorHexToInt($couleur0);
		$coul1 = $this->colorHexToInt($couleur1);
		$couleurOut[0] = $coul0[0] * $proucentage + $coul1[0] * (1-$proucentage);
		$couleurOut[1] = $coul0[1] * $proucentage + $coul1[1] * (1-$proucentage);
		$couleurOut[2] = $coul0[2] * $proucentage + $coul1[2] * (1-$proucentage);
		return ('#' . dechex($couleurOut[0]) . dechex($couleurOut[1]) . dechex($couleurOut[2]));
	}
	
	private function colorHexToInt($couleur) {
		$int = array();
		$int[0]=  hexdec(substr($couleur, 1, 2));
		$int[1]=  hexdec(substr($couleur, 3, 2));
		$int[2]=  hexdec(substr($couleur, 5, 2));
		return $int;
	}
		
}
