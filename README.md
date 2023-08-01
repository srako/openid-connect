# OIDC Connect

PHP implementation of https://openid.net/specs/openid-connect-core-1_0.html

## Install

Via [Composer](https://getcomposer.org/)

```bash
$ composer require digitalcz/openid-connect
```

## Usage

### Initialization
#### Using the OIDC discovery endpoint

```php
use DigitalCz\OpenIDConnect\ClientMetadata;
use DigitalCz\OpenIDConnect\ClientFactory;

$issuerUrl = 'https://example.com';
$clientMetadata = new ClientMetadata('clientid', 'clientsecret', 'https://example.com/callback');
$client = ClientFactory::create($issuerUrl, $clientMetadata);
```

<details>
<summary>Manually</summary>

```php
use DigitalCz\OpenIDConnect\Client;
use DigitalCz\OpenIDConnect\ClientMetadata;
use DigitalCz\OpenIDConnect\Config;
use DigitalCz\OpenIDConnect\Http\HttpClientFactory;
use DigitalCz\OpenIDConnect\Token\TokenVerifierFactory;
use DigitalCz\OpenIDConnect\ProviderMetadata;

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
use DigitalCz\OpenIDConnect\Param\AuthorizationParams;

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
use DigitalCz\OpenIDConnect\Param\CallbackParams;
use DigitalCz\OpenIDConnect\Param\CallbackChecks;

$tokens = $client->handleCallback(
    new CallbackParams($_GET),
    new CallbackChecks($_SESSION['oauth_state'])
);
```

### Client Credentials flow

```php
use DigitalCz\OpenIDConnect\Grant\ClientCredentials;
use DigitalCz\OpenIDConnect\Param\TokenParams;

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
