<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'echo' => [
            'type' => Type::string(),
            'args' => [
                'message' => [
                    'type' => Type::nonNull(Type::string()),
                ],
            ],
            'resolve' => static fn(array $rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
        ],
    ],
]);

$mutationType = new ObjectType([
    'name' => 'Mutation',
    'fields' => [
        'sum' => [
            'type' => Type::int(),
            'args' => [
                'x' => ['type' => Type::int()],
                'y' => ['type' => Type::int()],
            ],
            'resolve' => static fn(array $rootValue, array $args): int => $args['x'] + $args['y'],
        ],
    ],
]);

$schemaConfig = SchemaConfig::create()
    ->setQuery($queryType)
    ->setMutation($mutationType);

// See docs on schema options:
// https://webonyx.github.io/graphql-php/schema-definition/#configuration-options
$schema = new Schema($schemaConfig);

$rootValue = [
    'prefix' => 'You said: ',
];

$serverConfig = ServerConfig::create()
    ->setSchema($schema)
    ->setRootValue($rootValue);

// See docs on server options:
// https://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
$server = new StandardServer($serverConfig);

$server->handleRequest();
