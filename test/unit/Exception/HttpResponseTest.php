<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use VaclavVanik\Soap\Wsdl\Exception\HttpResponse;
use VaclavVanikTest\Soap\Wsdl\HttpProphecy;

final class HttpResponseTest extends TestCase
{
    use HttpProphecy;

    public function testCreate(): void
    {
        /** @var RequestInterface $request */
        $request = $this->prophesizeHttpRequest()->reveal();

        /** @var ResponseInterface $response */
        $response = $this->prophesizeHttpResponse();
        $response->getReasonPhrase()->willReturn('Reason text');
        $response = $response->reveal();

        $exception = HttpResponse::create($response, $request);

        $this->assertSame($response->getReasonPhrase(), $exception->getMessage());
        $this->assertSame($request, $exception->getRequest());
        $this->assertSame($response, $exception->getResponse());
    }
}
