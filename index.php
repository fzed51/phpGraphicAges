<?php

require './graphicAges.class.php';

$graph = new graphicAges(1000, 500);
$serie1 = [
	"0-9"=>10,
	"10-19"=>11,
	"20-29"=>11.5,
	"30-39"=>10.5,
	"40-49"=>11,
	"50-59"=>10.5,
	"60-69"=>8,
	"70-79"=>5,
	"80-89"=>3,
	"90-99"=>1,
	"100+"=>0.25	
];
$serie2 = [
	"0-9"=>9.5,
	"10-19"=>10.5,
	"20-29"=>11.5,
	"30-39"=>10,
	"40-49"=>11.5,
	"50-59"=>10,
	"60-69"=>7,
	"70-79"=>4,
	"80-89"=>2,
	"90-99"=>0.75,
	"100+"=>0.1	
];
$imageFileName = "./graph_ages.svg";
$graph->addSerie(GraphicAges::DROITE, $serie1, 'hommes');
$graph->addSerie(GraphicAges::GAUCHE, $serie2, 'femmes');
$graph->generateIn($imageFileName);

include('./vue.php');