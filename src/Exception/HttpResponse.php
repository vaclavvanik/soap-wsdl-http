<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl\Exception;

use Psr\Http\Message;
use RuntimeException;

final class HttpResponse extends RuntimeException implements Exception
{
    /** @var Message\ResponseInterface */
    private $response;

    /** @var Message\RequestInterface|null */
    private $request;

    private function __construct(
        Message\ResponseInterface $response,
        string $message,
        ?Message\RequestInterface $request = null
    ) {
        $this->request = $request;
        $this->response = $response;

        parent::__construct($message);
    }

    public static function create(
        Message\ResponseInterface $response,
        ?Message\RequestInterface $request = null
    ): self {
        return new self($response, $response->getReasonPhrase(), $request);
    }

    public function getRequest(): ?Message\RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): Message\ResponseInterface
    {
        return $this->response;
    }
}
