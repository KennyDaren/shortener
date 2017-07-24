<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(['127.0.0.1']);
$configurator->setDebugMode(TRUE);

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

if($configurator->isDebugMode() === TRUE) {
	tracyDump("#### ROBOT LOADER LOADED ####");
	$loader = $configurator->createRobotLoader();
	$loader->addDirectory(__DIR__)
	->register();
}

function tracyDump($object) {
	\Tracy\Debugger::barDump($object);
};

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/local.config.neon');

$container = $configurator->createContainer();


return $container;
