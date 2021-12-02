<?php

declare(strict_types=1);

namespace VaclavVanikTest\Soap\Wsdl\Exception;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\NetworkExceptionInterface;
use VaclavVanik\Soap\Wsdl\Exception\HttpConnection;
use VaclavVanikTest\Soap\Wsdl\HttpProphecy;

final class HttpConnectionTest extends TestCase
{
    use HttpProphecy;

    public function testFromNetworkException(): void
    {
        /** @var NetworkExceptionInterface $network */
        $network = $this->prophesizeNetworkExceptionWithRequest()->reveal();
        $exception = HttpConnection::fromNetworkException($network);

        $this->assertSame($network->getMessage(), $exception->getMessage());
        $this->assertSame($network->getCode(), $exception->getCode());
        $this->assertSame($network->getRequest(), $exception->getRequest());
        $this->assertSame($network, $exception->getPrevious());
    }
}
