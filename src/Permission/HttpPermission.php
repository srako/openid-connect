<?php
/**
 * http远程判断权限
 * @author srako
 * @date 2023/12/4 09:40
 * @page http://srako.github.io
 */
declare(strict_types=1);

namespace Srako\OpenIDConnect\Permission;

use Psr\Http\Client\ClientExceptionInterface;
use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\Exception\HttpException;
use Srako\OpenIDConnect\Http\AuthenticatedClient;
use Srako\OpenIDConnect\ProviderMetadata;

class HttpPermission implements Permission
{
    protected AuthenticatedClient $authenticatedClient;
    protected ProviderMetadata $providerMetadata;

    public function __construct(AuthenticatedClient $authenticatedClient)
    {
        $this->authenticatedClient = $authenticatedClient;
        $this->providerMetadata = $authenticatedClient->client()->getConfig()->providerMetadata();
    }

    public function userinfo(): array
    {
        return $this->authenticatedClient->client()->userInfo($this->authenticatedClient->tokens());
    }

    /**
     * 判断用户的权限
     * @param string $method
     * @param string $route
     * @return bool
     */
    public function can(string $method, string $route): bool
    {
        $method = strtoupper($method);
        $request = $this->authenticatedClient
            ->httpClient()
            ->createRequest('GET',
                $this->providerMetadata->issuer() . '/api/user/check_permission?' .
                $this->authenticatedClient->httpClient()->buildQueryString(compact('method', 'route'))
            );
        try {
            $response = $this->authenticatedClient->sendRequest($request);
            return $response->getStatusCode() === 200;
        } catch (HttpException $httpException) {
            if ($httpException->getResponse()->getStatusCode() === 403) {
                return false;
            }
            throw $httpException;
        } catch (ClientExceptionInterface $e) {
            throw new AuthorizationException('Permission request client error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 获取token的所有权限列表
     * @return array
     */
    public function permissions(): array
    {
        $request = $this->authenticatedClient
            ->httpClient()
            ->createRequest('GET', $this->providerMetadata->issuer() . '/api/user/permission');
        try {
            $response = $this->authenticatedClient->sendRequest($request);
            return $this->authenticatedClient->httpClient()->parseResponse($response);
        } catch (ClientExceptionInterface $e) {
            throw new AuthorizationException('Data Permission request client error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 获取用户的数据权限
     * @param string|null $route
     * @param string|null $method
     * @return array
     */
    public function dataPermissions(string $route = null, string $method = null): array
    {
        $query = '';
        if ($route && $method) {
            $query = $this->authenticatedClient->httpClient()->buildQueryString(compact('route', 'method'));
        }
        $request = $this->authenticatedClient
            ->httpClient()
            ->createRequest('GET', $this->providerMetadata->issuer() . '/api/user/data_permission?' . $query);
        try {
            $response = $this->authenticatedClient->sendRequest($request);
            return $this->authenticatedClient->httpClient()->parseResponse($response);
        } catch (ClientExceptionInterface $e) {
            throw new AuthorizationException('Data Permission request client error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function logout(): void
    {
        $request = $this->authenticatedClient
            ->httpClient()
            ->createRequest(
                'GET', $this->providerMetadata->endSessionEndpoint() . '?id_token_hint=' .
                $this->authenticatedClient->tokens()->idToken()
            );
        try {
            $this->authenticatedClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new AuthorizationException('Data Permission request client error: ' . $e->getMessage(), 0, $e);
        }
    }
}