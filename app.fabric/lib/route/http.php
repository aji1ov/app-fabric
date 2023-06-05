<?php

namespace App\Fabric\Route;

use App\Fabric\Route\Process\Request;
use App\Fabric\Route\Process\Response;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;

class Http
{
    public static function createRequestViaHttpRequest(HttpRequest $request): Request
    {
        $url = new Request\Url(static::makeRequestAbsoluteUrl($request),  static::getRequestPath($request));
        $query = new Request\Query($request->getServer()->get('QUERY_STRING'), $request->getQueryList()->toArray());
        $body = new Request\Body($request->getInput(), $request->getPostList()->toArray());

        return new Request($url, $query, $body, $request->getRequestMethod());
    }

    private static function makeRequestAbsoluteUrl(HttpRequest $request): string
    {
        return $request->getServer()->get('REQUEST_SCHEME')."://".$request->getHttpHost()."/".$request->getRequestUri();
    }

    private static function getRequestPath(HttpRequest $request): string
    {
        list($page, $query) = explode("?", $request->getRequestUri());
        $path = "/".trim($page, "/")."/";

        return $path;
    }

    public static function createHttpResponse(Response $resp): HttpResponse
    {
        $response = new HttpResponse();
        $response->setStatus($resp->status());
        $response->setContent($resp->body());

        foreach($resp->getHeaders() as $name => $value)
        {
            $response->addHeader($name, $value);
        }

        return $response;
    }
}