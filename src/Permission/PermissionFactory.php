<?php
/**
 * 权限相关工厂类
 * @author srako
 * @date 2023/12/4 09:32
 * @page http://srako.github.io
 */

declare(strict_types=1);

namespace Srako\OpenIDConnect\Permission;

use Psr\SimpleCache\CacheInterface;
use Srako\OpenIDConnect\Http\AuthenticatedClient;

class PermissionFactory
{
    public static function create(AuthenticatedClient $httpClient, ?CacheInterface $cache = null)
    {

        if ($cache !== null) {
            $permission = new CachedPermission($httpClient, $cache);
        } else {
            $permission = new HttpPermission($httpClient);

        }
        return $permission;
    }
}