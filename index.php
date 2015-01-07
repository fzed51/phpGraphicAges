<?php

require './graphicAges.class.php';

$graph = new graphicAges(600, 400, 20, 12);
$ordonnee = [
	"0-9",
	"10-19",
	"20-29",
	"30-39",
	"40-49",
	"50-59",
	"60-69",
	"70-79",
	"80-89",
	"90-99",
	"100+"
];
$serie1 = [
	10,
	11,
	11.5,
	10.5,
	11,
	10.5,
	8,
	5,
	3,
	1,
	0.25	
];
$serie2 = [
	9.5,
	10.5,
	11.5,
	10,
	11.5,
	10,
	7,
	4,
	2,
	0.75,
	0.1	
];
$imageFileName = "./graph_ages.svg";
$graph->setOrdonnee($ordonnee);
$graph->addSerie(GraphicAges::DROITE, $serie1, 'hommes (millions)', '#8888ff');
$graph->addSerie(GraphicAges::GAUCHE, $serie2, 'femmes (millions)', '#ff8888');
$graph->generateIn($imageFileName);

include('./vue.php');