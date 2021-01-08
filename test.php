<?php

require_once __DIR__ . '/CollectionInterface.php';
require_once __DIR__ . '/Collection/RouteCollection.php';
require_once __DIR__ . '/ParserInterface.php';
require_once __DIR__ . '/Parser/JsonParser.php';
require_once __DIR__ . '/Dispatcher.php';
require_once __DIR__ . '/Route.php';


$dispatcher = new Swiftly\Routing\Dispatcher(
    new Swiftly\Routing\Parser\JsonParser
);

$dispatcher->load( __DIR__ . '/test.routes.json' );

// Should return the 'home' route
$dispatcher->dispatch( "GET", '/' );

// Should return the 'test_number' route
$dispatcher->dispatch( "GET", '/test/145' );

// Should return the 'test_string' route
$dispatcher->dispatch( "GET", '/test/a-string' );
