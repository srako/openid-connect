<?php
/**
 *
 * @author srako
 * @date 2023/12/4 12:48
 * @page http://srako.github.io
 */

if (!function_exists('route_match')) {
    /**
     * 路由匹配，支持变量{id}{ids}{alpha}{alpha_dash}
     * @param string $route
     * @param string $query
     * @return bool
     */
    function route_match(string $route, string $query): bool
    {
        $variableName = ['{id}', '{ids}', '{alpha}', '{alpha_dash}'];
        $variable = ['[1-9]\d*', '[1-9]\d*(,[1-9]\d*)*', '[a-zA-Z]+', '[0-9a-zA-Z_-]+'];
        $pattern = str_replace($variableName, $variable, $route);
        return (bool)preg_match('#^' . $pattern . '\z#u', $query);
    }
}