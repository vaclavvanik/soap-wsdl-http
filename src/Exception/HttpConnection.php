<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl\Exception;

use Psr\Http\Client;
use Psr\Http\Message;
use RuntimeException;
use Throwable;

final class HttpConnection extends RuntimeException implements Exception, Client\NetworkExceptionInterface
{
    /** @var Message\RequestInterface */
    private $request;

    private function __construct(
        Message\RequestInterface $request,
        string $message,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->request = $request;

        parent::__construct($message, $code, $previous);
    }

    public static function fromNetworkException(Client\NetworkExceptionInterface $e): self
    {
        return new self($e->getRequest(), $e->getMessage(), $e->getCode(), $e);
    }

    public function getRequest(): Message\RequestInterface
    {
        return $this->request;
    }
}
