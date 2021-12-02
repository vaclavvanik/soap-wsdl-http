<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

trait HttpProphecy
{
    use ProphecyTrait;

    private function prophesizeHttpClient(): ProphecyInterface
    {
        return $this->prophesize(ClientInterface::class);
    }

    private function prophesizeHttpClientSendRequest(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null
    ): ProphecyInterface {
        if ($request === null) {
            $request = $this->prophesizeHttpRequest()->reveal();
        }

        if ($response === null) {
            $response = $this->prophesizeHttpResponse()->reveal();
        }

        /** @var ProphecyInterface|ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClient();
        $httpClient->sendRequest($request)->willReturn($response);

        return $httpClient;
    }

    private function prophesizeHttpClientSendRequestThrowsException(
        RequestInterface $request,
        Throwable $e
    ): ProphecyInterface {
        /** @var ProphecyInterface|ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClient();
        $httpClient->sendRequest($request)->willThrow($e);

        return $httpClient;
    }

    private function prophesizeHttpRequest(): ProphecyInterface
    {
        return $this->prophesize(RequestInterface::class);
    }

    private function prophesizeHttpRequestFactory(): ProphecyInterface
    {
        return $this->prophesize(RequestFactoryInterface::class);
    }

    private function prophesizeHttpRequestFactoryCreateRequest(
        string $method,
        string $uri,
        RequestInterface $request
    ): ProphecyInterface {
        /** @var ProphecyInterface|RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactory();
        $requestFactory->createRequest($method, $uri)->willReturn($request);

        return $requestFactory;
    }

    private function prophesizeHttpResponse(): ProphecyInterface
    {
        return $this->prophesize(ResponseInterface::class);
    }

    private function prophesizeHttpResponseWithBody(string $body): ProphecyInterface
    {
        /** @var ProphecyInterface|ResponseInterface $response */
        $response = $this->prophesizeHttpResponse();
        $response->getBody()->willReturn($body);

        return $response;
    }

    private function prophesizeNetworkException(): ProphecyInterface
    {
        return $this->prophesize(NetworkExceptionInterface::class);
    }

    private function prophesizeNetworkExceptionWithRequest(
        ?RequestInterface $request = null
    ): ProphecyInterface {
        if ($request === null) {
            $request = $this->prophesizeHttpRequest()->reveal();
        }

        /** @var ProphecyInterface|NetworkExceptionInterface $exception */
        $exception = $this->prophesizeNetworkException();
        $exception->getRequest()->willReturn($request);

        return $exception;
    }
}
