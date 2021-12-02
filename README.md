# Soap WSDL http

This package provides an easy way to get WSDL from URL.

## Install

You can install this package via composer.

``` bash
composer require vaclavvanik/soap-wsdl-http
```

This package needs [PSR-17 HTTP Factories](https://www.php-fig.org/psr/psr-17/) and [PSR-18 HTTP Client](https://www.php-fig.org/psr/psr-18/) implementations.
You can use e.g. [Guzzle](https://github.com/guzzle/guzzle).

``` bash
composer require vaclavvanik/soap-wsdl-http guzzlehttp/guzzle
```

## Usage

### HttpProvider

GET WSDL from given URL. Example with Guzzle:

```php
<?php

declare(strict_types=1);

use GuzzleHttp;
use VaclavVanik\Soap\Wsdl;

$httpClient = new GuzzleHttp\Client();
$requestFactory = new GuzzleHttp\Psr7\HttpFactory();
$url = 'https://example.com/my.wsdl';

$wsdl = (new Wsdl\HttpClientProvider($httpClient, $requestFactory, $url))->provide();
```

## Exceptions

provide methods throw:

- [Exception\HttpConnection](src/Exception/HttpConnection.php) if http connection issue occurred.
- [Exception\HttpResponse](src/Exception/HttpResponse.php) if unexpected response returned.

## Run check - coding standards and php-unit

Install dependencies:

```bash
make install
```

Run check:

```bash
make check
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
