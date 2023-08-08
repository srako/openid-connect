# OIDC Connect

PHP implementation of https://openid.net/specs/openid-connect-core-1_0.html

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require srako/openid-connect
```

## Usage

### Initialization
#### Using the OIDC discovery endpoint

```php
use Srako\OpenIDConnect\ClientMetadata;
use Srako\OpenIDConnect\ClientFactory;

$issuerUrl = 'https://example.com';
$clientMetadata = new ClientMetadata('clientid', 'clientsecret', 'https://example.com/callback');
$client = ClientFactory::create($issuerUrl, $clientMetadata);
```

<details>
<summary>Manually</summary>

```php
use Srako\OpenIDConnect\Client;
use Srako\OpenIDConnect\ClientMetadata;
use Srako\OpenIDConnect\Config;
use Srako\OpenIDConnect\Http\HttpClientFactory;
use Srako\OpenIDConnect\Token\TokenVerifierFactory;
use Srako\OpenIDConnect\ProviderMetadata;

$clientMetadata = new ClientMetadata('clientid', 'clientsecret', 'https://example.com/callback');
$providerMetadata = new ProviderMetadata([
    ProviderMetadata::AUTHORIZATION_ENDPOINT => 'https://example.com/authorize',
    ProviderMetadata::TOKEN_ENDPOINT => 'https://example.com/token',
    // ...
])
$config = new Config($providerMetadata, $clientMetadata);
$client = new Client($config, HttpClientFactory::create());
```
</details>

### Authorization Code flow

#### Step 1 - Redirect the user to authorization endpoint

```php
use Srako\OpenIDConnect\Param\AuthorizationParams;

$state = bin2hex(random_bytes(8));
$_SESSION['oauth_state'] = $state;

$authorizationParams = new AuthorizationParams([
    AuthorizationParams::SCOPE => 'openid profile',
    AuthorizationParams::STATE => $state,
]);

$url = $client->getAuthorizationUrl($authorizationParams); 
header('Location: ' . $url);
exit();
```

#### Step 2 - Handle callback and exchange code for tokens

```php
use Srako\OpenIDConnect\Param\CallbackParams;
use Srako\OpenIDConnect\Param\CallbackChecks;

$tokens = $client->handleCallback(
    new CallbackParams($_GET),
    new CallbackChecks($_SESSION['oauth_state'])
);
```

### Client Credentials flow

```php
use Srako\OpenIDConnect\Grant\ClientCredentials;
use Srako\OpenIDConnect\Param\TokenParams;

$tokens = $client->requestTokens(
    new TokenParams(
        new ClientCredentials(),
        [
            TokenParams::SCOPE => 'some scope'
        ]
    )
);
```

See [examples](examples) for more


## Credits

- [Digital Solutions s.r.o.][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[link-author]: https://github.com/srako
