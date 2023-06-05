<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Process\Request;
use Bitrix\Main\HttpRequest;

class Http
{
    public static function createRequestViaHttpRequest(HttpRequest $request): Request
    {
        $url = new Request\Url(static::makeRequestAbsoluteUrl($request), $path, $result->getDefinitions());
        $query = new Request\Query($request->getServer()->get('QUERY_STRING'), $request->getQueryList()->toArray());
        $body = new Request\Body($request->getInput(), $request->getPostList()->toArray());

        return new Request($url, $query, $body);
    }

    private static function makeRequestAbsoluteUrl(HttpRequest $request): string
    {
        return $request->getServer()->get('REQUEST_SCHEME')."://".$request->getHttpHost()."/".$request->getRequestUri();
    }

    private static function getRequestPath(string $rootUrl, HttpRequest $request): string
    {
        list($page, $query) = explode("?", $request->getRequestUri());
        $path = "/".trim(str_replace($rootUrl, '', $page), "/")."/";

        return $path;
    }
}