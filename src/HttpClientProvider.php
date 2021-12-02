<?php

declare(strict_types=1);

namespace VaclavVanik\Soap\Wsdl;

use Psr\Http;
use Throwable;

final class HttpClientProvider implements WsdlResourceProvider
{
    /** @var Http\Client\ClientInterface */
    private $client;

    /** @var Http\Message\RequestFactoryInterface */
    private $requestFactory;

    /** @var string */
    private $uri;

    /** @throws Exception\ValueError */
    public function __construct(
        Http\Client\ClientInterface $client,
        Http\Message\RequestFactoryInterface $requestFactory,
        string $uri
    ) {
        if ($uri === '') {
            throw new Exception\ValueError('Uri cannot be empty');
        }

        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uri = $uri;
    }

    public function provide(): string
    {
        try {
            $request = $this->requestFactory->createRequest('GET', $this->uri);
            $response = $this->client->sendRequest($request);
        } catch (Http\Client\NetworkExceptionInterface $e) {
            throw Exception\HttpConnection::fromNetworkException($e);
        } catch (Throwable $e) {
            throw Exception\Runtime::fromThrowable($e);
        }

        if ($response->getStatusCode() > 299 || $response->getStatusCode() < 200) {
            throw Exception\HttpResponse::create($response, $request);
        }

        $wsdl = (string) $response->getBody();

        Utils::checkWsdl($wsdl, $this->uri);

        return $wsdl;
    }

    public function resource(): string
    {
        return $this->uri;
    }
}
