<?php declare(strict_types = 1);
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
    if (!$lang) {
        $lang = $container->getService('httpRequest')->detectLanguage(array('en', 'cs')) ?: 'cs';
        return $presenter->redirectUrl($lang);
    }

    $header = $container->getService('httpResponse')->getHeader('Content-Security-Policy');

    // create template
    /** @var \Nette\Bridges\ApplicationLatte\Template $template */
    $template = $presenter->createTemplate();
    $latte = $template->getLatte();
    preg_match('#\s\'nonce-([\w+/]+=*)\'#', (string) $header, $m);
    $latte->addProvider('uiNonce', $m[1]);
    Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());

    return $template->setFile(__DIR__ . '/app/' . $lang . '.latte');
});


// Run the application!
$container->getService('application')->run();