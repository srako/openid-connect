<?php

declare(strict_types=1);

use Srako\OpenIDConnect\ClientFactory;
use Srako\OpenIDConnect\ClientMetadata;
use Srako\OpenIDConnect\Param\CallbackChecks;
use Srako\OpenIDConnect\Param\CallbackParams;

require dirname(__DIR__) . '/vendor/autoload.php';

$issuerUrl = 'https://accounts.google.com';
$clientMetadata = new ClientMetadata('clientid', 'clientsecret', 'https://example.com/callback');
$client = ClientFactory::create($issuerUrl, $clientMetadata);

// Parameters that were returned from authorization server
// $parameters = $request->query->all();
$parameters = ['state' => 'foo', 'code' => 'bar'];

$tokens = $client->handleCallback(
    new CallbackParams($parameters),
    new CallbackChecks('foo', 'bar'),
);
var_dump($tokens);


$userinfo = $client->userInfo($tokens);
var_dump($userinfo);
