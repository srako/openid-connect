<?php
/**
 *
 * @author srako
 * @date 2023/12/4 09:22
 * @page http://srako.github.io
 */


declare(strict_types=1);

use Srako\OpenIDConnect\ClientFactory;
use Srako\OpenIDConnect\ClientMetadata;

require dirname(__DIR__) . '/vendor/autoload.php';

$issuerUrl = 'https://accounts.google.com';
$clientMetadata = new ClientMetadata('clientid', 'clientsecret', 'https://example.com/callback');
$client = ClientFactory::create($issuerUrl, $clientMetadata);

$permission = $client->permission(new \Srako\OpenIDConnect\Token\Tokens([
    'access_token' => 'token',
    'id_token' => 'token'
]));
$can = $permission->can('GET', 'user.index');
var_dump($can);

$dataPermissions = $permission->dataPermissions();
var_dump($dataPermissions);


//$userinfo = $client->userInfo($tokens);
//var_dump($userinfo);
