<?php

namespace App\Fabric\Route\Reject;

use App\Fabric\Error\ApiException;
use App\Fabric\Route\Process\Response;

class Html implements RejectInterface
{
    private function createHtml($title, $body)
    {
        return '<!doctype html>
            <html>
                <head>
                    <title>'.$title.'</title>
                </head>
                <body>
                    '.$body.'
                </body>
            </html>';
    }

    public function format(ApiException $exception): Response
    {
        return (new Response($exception->getStatusCode()))
            ->raw($this->createHtml($exception->getMessage(), '<h1>'.$exception->getMessage().'</h1><p>'.$exception->getErrorCode().'</p>'))
            ->header('Content-Type', 'text/html');
    }
}