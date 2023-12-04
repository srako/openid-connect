<?php
/**
 * 本地缓存判断权限
 * @author srako
 * @date 2023/12/4 11:13
 * @page http://srako.github.io
 */
declare(strict_types=1);

namespace Srako\OpenIDConnect\Permission;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\SimpleCache\CacheInterface;
use Srako\OpenIDConnect\ClientMetadata;
use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\Http\AuthenticatedClient;

class CachedPermission extends HttpPermission
{

    private CacheInterface $cache;
    private string $jti;
    private int $ttl;

    public function __construct(AuthenticatedClient $authenticatedClient, CacheInterface $cache)
    {
        parent::__construct($authenticatedClient);
        $this->cache = $cache;
        $this->introspection();
    }


    public function can($method, $route): bool
    {
        $userinfo = $this->userinfo();
        if ($userinfo['is_administrator']) {
            return true;
        }

        $permissions = $this->permissions();
        if (empty($permissions)) {
            return false;
        }
        $method = strtoupper($method);
        foreach ($permissions as $permission) {
            if ($permission['method'] === $method && route_match($permission['route'], $route)) {
                return true;
            }
        }
        return false;
    }

    public function dataPermissions(): array
    {
        $key = 'oidc_data_permission_' . $this->jti;
        if ($dataPermissions = $this->cache->get($key)) {
            return $dataPermissions;
        }
        $dataPermissions = parent::dataPermissions();
        $this->cache->set($key, $dataPermissions, $this->ttl);
        return $dataPermissions;
    }

    public function permissions(): array
    {
        $key = 'oidc_permission_' . $this->jti;
        if ($permissions = $this->cache->get($key)) {
            return $permissions;
        }

        $permissions = parent::permissions();
        $this->cache->set($key, $permissions, $this->ttl);
        return $permissions;
    }

    public function userinfo(): array
    {
        $key = 'oidc_userinfo_' . $this->jti;
        if ($userinfo = $this->cache->get($key)) {
            return $userinfo;
        }
        $userinfo = parent::userinfo();
        $this->cache->set($key, $userinfo, $this->ttl);
        return $userinfo;
    }


    private function introspection()
    {
        $claims = $this->authenticatedClient->client()->handleEvent($this->authenticatedClient->tokens()->idToken());
        $this->jti = $claims->getString('jti');
        $this->ttl = $claims->getInt('exp') - time() - 60;

        // token缓存时效为30分钟，超过30分钟需要远程校验
        $key = 'oidc_introspection_' . $this->jti;
        if ($this->cache->has($key)) {
            return;
        }
        $request = $this->authenticatedClient
            ->httpClient()
            ->createRequest('POST', $this->providerMetadata->introspectionEndpoint())
            ->withBody($this->authenticatedClient->httpClient()->createStream(
                $this->authenticatedClient->httpClient()->buildQueryString([
                    ClientMetadata::CLIENT_ID => $this->authenticatedClient->client()->getConfig()->clientMetadata()->id(),
                    ClientMetadata::CLIENT_SECRET => $this->authenticatedClient->client()->getConfig()->clientMetadata()->secret(),
                    'token' => $this->authenticatedClient->tokens()->idToken(),
                    'token_type_hint' => 'access_token'
                ])
            ));
        try {
            $response = $this->authenticatedClient->sendRequest($request);
            $result = $this->authenticatedClient->httpClient()->parseResponse($response);
            if (isset($result['active']) && $result['active']) {
                $this->cache->set($key, 1, min($this->ttl, 1800));
                return;
            }
            // 删除所有缓存
            $this->cache->deleteMultiple([
                'oidc_userinfo_' . $this->jti,
                'oidc_permission_' . $this->jti,
                'oidc_data_permission_' . $this->jti,
            ]);
            throw new AuthorizationException('Introspection request client error: ' . $result['error'] ?? '', 0);
        } catch (ClientExceptionInterface $e) {
            // 删除所有缓存
            $this->cache->deleteMultiple([
                'oidc_userinfo_' . $this->jti,
                'oidc_permission_' . $this->jti,
                'oidc_data_permission_' . $this->jti,
            ]);
            throw new AuthorizationException('Introspection request client error: ' . $e->getMessage(), 0, $e);
        }
    }

}