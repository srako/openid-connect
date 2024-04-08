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
    private string $sub;
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
        if (!$this->sub) {
            return true;
        }
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

    public function dataPermissions(string $route = null, string $method = null): array
    {
        $key = 'data_permission_' . $route.'_'.$method;
        if ($dataPermissions = $this->getCache($key)) {
            return $dataPermissions;
        }
        $dataPermissions = parent::dataPermissions($route, $method);
        $this->setCache($key, $dataPermissions);
        return $dataPermissions;
    }

    public function permissions(): array
    {
        $key = 'permission' ;
        if ($permissions = $this->getCache($key)) {
            return $permissions;
        }
        $permissions = parent::permissions();
        $this->setCache($key, $permissions);
        return $permissions;
    }

    public function userinfo(): array
    {
        $key = 'userinfo' ;
        if ($userinfo = $this->getCache($key)) {
            return $userinfo;
        }
        $userinfo = parent::userinfo();
        $this->setCache($key, $userinfo);
        return $userinfo;
    }


    private function introspection()
    {
        $claims = $this->authenticatedClient->client()->handleEvent($this->authenticatedClient->tokens()->idToken());
        $this->sub = $claims->getString('sub');
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
            $this->clearCache();
            throw new AuthorizationException('Introspection request client error: ' . $result['error'] ?? '', 0);
        } catch (ClientExceptionInterface $e) {
            // 删除所有缓存
            $this->clearCache();
            throw new AuthorizationException('Introspection request client error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function logout(): void
    {
        $this->clearCache();
        parent::logout();
    }

    /**
     * 增加缓存，并且绑定缓存至jti
     * @param $key
     * @param $value
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function setCache($key, $value)
    {
        $key = 'oidc_' . md5($key) . '_' . $this->jti;
        $this->cache->set($key, $value, $this->ttl);

        // 获取已经存在的缓存键值
        $cacheKeys = $this->cache->get('oidc_' . $this->jti);
        if ($cacheKeys) {
            $cacheKeys .= ',' . $key;
        } else {
            $cacheKeys = $key;
        }
        $this->cache->set('oidc_' . $this->jti, $cacheKeys, $this->ttl);
    }

    private function getCache($key)
    {
        return $this->cache->get('oidc_' . md5($key) . '_' . $this->jti);
    }

    private function clearCache()
    {
        $cacheKeys = $this->cache->get('oidc_' . $this->jti);
        if($cacheKeys){
            $this->cache->deleteMultiple(explode(',', $cacheKeys));
        }
    }
}