<?php
/**
 *
 * @author srako
 * @date 2023/12/4 11:10
 * @page http://srako.github.io
 */

namespace Srako\OpenIDConnect\Permission;

interface Permission
{

    public function userinfo(): array;

    /**
     * 判断用户的权限
     * @param string $method
     * @param string $route
     * @return bool
     */
    public function can(string $method, string $route): bool;


    /**
     * 获取token的所有权限列表
     * @return array
     */
    public function permissions(): array;


    /**
     * 获取用户的数据权限
     * @return array
     */
    public function dataPermissions(): array;

    /**
     * 用户退出登陆
     * @return void
     */
    public function logout(): void;
}