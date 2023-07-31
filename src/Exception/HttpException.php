<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Exception;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpException extends RuntimeException implements ClientExceptionInterface
{
    private RequestInterface $request;
    private ResponseInterface $response;

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct(sprintf(
            '%s %s returned for %s %s %s',
            $response->getStatusCode(),
            $response->getReasonPhrase(),
            $request->getMethod(),
            $request->getUri(),
            $response->getBody(),
        ));
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
