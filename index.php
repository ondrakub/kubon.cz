<?php
//require ".maintenance.php";

// Load libraries
require __DIR__ . '/vendor/autoload.php';


$configurator = new Nette\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(__DIR__ . '/app/log');

// Configure libraries

$configurator->setTempDirectory(__DIR__ . '/app/temp');

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/app/config.neon');
$container = $configurator->createContainer();


// Setup routes

$router = $container->getService('router');
$router[] = new Nette\Application\Routers\Route('[<lang (?-i)cs|en>]', function($presenter, $lang) use ($container) {
    $httpRequest = $container->getService('httpRequest');

    if (!$lang) {
        $lang = $httpRequest->detectLanguage(array('en', 'cs')) ?: 'cs';
        return $presenter->redirectUrl($lang);
    }


    // create template
    $template = $presenter->createTemplate()
        ->setFile(__DIR__ . '/app/' . $lang . '.latte');

    return $template;
});


// Run the application!
$container->getService('application')->run();