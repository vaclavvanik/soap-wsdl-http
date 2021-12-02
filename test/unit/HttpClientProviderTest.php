<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use VaclavVanik\Soap\Wsdl\Exception\HttpConnection;
use VaclavVanik\Soap\Wsdl\Exception\HttpResponse;
use VaclavVanik\Soap\Wsdl\Exception\Runtime;
use VaclavVanik\Soap\Wsdl\Exception\ValueError;
use VaclavVanik\Soap\Wsdl\HttpClientProvider;

final class HttpClientProviderTest extends TestCase
{
    use HttpProphecy;

    /** @var string */
    private $wsdlUrl = 'https://example.com';

    /** @var string */
    private $wsdlContent = '<root/>';

    public function testThrowValueErrorExceptionIfEmptyUri(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClient()->reveal();
        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactory()->reveal();

        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('Uri cannot be empty');

        new HttpClientProvider($httpClient, $requestFactory, '');
    }

    public function testProvide(): void
    {
        /** @var RequestInterface $httpRequest */
        $httpRequest = $this->prophesizeHttpRequest()->reveal();

        /** @var ResponseInterface $httpResponse */
        $httpResponse = $this->prophesizeHttpResponseWithBody($this->wsdlContent);
        $httpResponse->getStatusCode()->willReturn(200);
        $httpResponse->getStatusCode()->willReturn(200);
        $httpResponse = $httpResponse->reveal();

        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClientSendRequest($httpRequest, $httpResponse)->reveal();

        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactoryCreateRequest(
            'GET',
            $this->wsdlUrl,
            $httpRequest,
        )->reveal();

        $httpClientProvider = new HttpClientProvider($httpClient, $requestFactory, $this->wsdlUrl);

        $this->assertSame($this->wsdlContent, $httpClientProvider->provide());
    }

    public function testResource(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClient()->reveal();

        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactory()->reveal();

        $httpClientProvider = new HttpClientProvider($httpClient, $requestFactory, $this->wsdlUrl);

        $this->assertSame($this->wsdlUrl, $httpClientProvider->resource());
    }

    public function testProvideThrowsRuntimeException(): void
    {
        /** @var RequestInterface $httpRequest */
        $httpRequest = $this->prophesizeHttpRequest()->reveal();

        $exception = new RuntimeException('message');

        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClientSendRequestThrowsException($httpRequest, $exception)->reveal();

        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactoryCreateRequest(
            'GET',
            $this->wsdlUrl,
            $httpRequest,
        )->reveal();

        $this->expectException(Runtime::class);

        (new HttpClientProvider($httpClient, $requestFactory, $this->wsdlUrl))->provide();
    }

    public function testProvideThrowsHttpConnectionException(): void
    {
        /** @var RequestInterface $httpRequest */
        $httpRequest = $this->prophesizeHttpRequest()->reveal();

        /** @var NetworkExceptionInterface $exception */
        $exception = $this->prophesizeNetworkExceptionWithRequest($httpRequest)->reveal();

        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClientSendRequestThrowsException($httpRequest, $exception)->reveal();

        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactoryCreateRequest(
            'GET',
            $this->wsdlUrl,
            $httpRequest,
        )->reveal();

        $this->expectException(HttpConnection::class);

        (new HttpClientProvider($httpClient, $requestFactory, $this->wsdlUrl))->provide();
    }

    public function testProvideThrowsHttpResponseException(): void
    {
        /** @var RequestInterface $httpRequest */
        $httpRequest = $this->prophesizeHttpRequest()->reveal();

        /** @var ResponseInterface $httpResponse */
        $httpResponse = $this->prophesizeHttpResponseWithBody($this->wsdlContent);
        $httpResponse->getStatusCode()->shouldBeCalled()->willReturn(404);
        $httpResponse->getReasonPhrase()->shouldBeCalled()->willReturn('Not found');
        $httpResponse = $httpResponse->reveal();

        /** @var ClientInterface $httpClient */
        $httpClient = $this->prophesizeHttpClientSendRequest($httpRequest, $httpResponse)->reveal();

        /** @var RequestFactoryInterface $requestFactory */
        $requestFactory = $this->prophesizeHttpRequestFactoryCreateRequest(
            'GET',
            $this->wsdlUrl,
            $httpRequest,
        )->reveal();

        $this->expectException(HttpResponse::class);

        (new HttpClientProvider($httpClient, $requestFactory, $this->wsdlUrl))->provide();
    }
}
