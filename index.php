<?php
//require ".maintenance.php";
use Nette\Application\Routers\Route,
	Nette\Mail\Message;


// Load libraries
require __DIR__ . '/app/libs/nette.phar';


$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(__DIR__ . '/app/log');

// Configure libraries

$configurator->setTempDirectory(__DIR__ . '/app/temp');

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/app/config.neon');
$container = $configurator->createContainer();


// Setup routes
$router = $container->getService('router');
$router[] = new Route('[<lang (?-i)cs|en>]', function($presenter, $lang) use ($container) {
    $httpRequest = $container->getService('httpRequest');
    $post = $httpRequest->getPost();
    if (isset($post['name']) && isset($post['message']) && isset($post['email'])){
        $mail = new Message;
        $mail->setFrom($post['name'] . ' <'.$post['email'].'>')
            ->addTo($container->parameters['email'])
            ->setSubject('Zpráva z webu')
            ->setBody($post['message'])
            ->send();
    }

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