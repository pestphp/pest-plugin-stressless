This repository contains the Pest Plugin Stresless.

> If you want to start testing your application with Pest, visit the main **[Pest Repository](https://github.com/pestphp/pest)**.

- Explore our docs at **[pestphp.com »](https://pestphp.com)**
- Follow us on Twitter at **[@pestphp »](https://twitter.com/pestphp)**
- Join us at **[discord.gg/kaHY6p54JH »](https://discord.gg/kaHY6p54JH)** or **[t.me/+kYH5G4d5MV83ODk0 »](https://t.me/+kYH5G4d5MV83ODk0)**

Pest is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.

---

# Stress Testing

**Source code**: [github.com/pestphp/pest-plugin-stressless](https://github.com/pestphp/pest-plugin-stressless)

Stress Testing is a type of testing that verifies the stability and reliability of your application under realistic or extreme conditions. For example, you can use stress testing to verify that your application can handle a large number of requests or that it can handle a large amount of data.

To start using Pest's Stress Testing plugin (mostly known as Stressless), you need to require the plugin via Composer.

```bash
composer require pestphp/pest-plugin-stressless --dev
```

After requiring the plugin, you can perform a stress test in two ways:

- [Using the `stress` command](#the-stress-command): Useful when you want to quickly stress test a specific endpoint, and have a detailed output on the terminal.
- [Using the `stress()` method](#the-stress-function): Useful when you want to set expectations on a stress test result.

## The `stress` command

To get started with stress testing, you may use the `stress` command. The command receives one argument: the URL to stress test.

```bash
./vendor/bin/pest stress example.com
```

By default, the stress test duration will be 10 seconds. However, you may customize this value using the `--duration` option:

```bash
./vendor/bin/pest stress example.com --duration=30
```

In addition, the number of concurrent requests will be 1. However, you may also customize this value using the `--concurrency` option:

```bash
./vendor/bin/pest stress example.com --concurrency=5
```

The concurrency value represents the number of concurrent requests that will be made to the given URL. For example, if you set the concurrency to 5, Pest will **constantly make 5 concurrent requests** to the given URL until the stress test duration is reached.

You may want to be mindful of the number of concurrent requests you configure. If you configure too many concurrent requests, you may overwhelm your application, server or hit rate limits.

## The `stress()` function

Once you start understanding how stress testing works, you may want to start setting expectations on the stress test result. For example, you may want to verify that the average response time is *always* less than 100ms, and this is where the `stress()` function comes in.

To get started, simply create a regular test and use the `stress()` function to stress test a given URL:

```php
<?php

use function Pest\Stressless\stress;

it('has a fast response time', function () {
    $result = stress('example.com')->for(10)->seconds();
    
    expect($result->responseTime())->toBeLessThan(100);
});
```

The `stress()` function return the stress test result, which you can use to set expectations. Here is the list of available methods:

## Request Duration

Returns the overall request duration in milliseconds.

```php
$result->requests()->duration()->avg()
                               ->min()
                               ->med()    
                               ->max()
                               ->p90()
                               ->p95();
```

## Requests Count

Returns the number of requests made.

```php
$result->requests()->count();
```

## Requests Rate

Returns the number of requests made per second.

```php
$result->requests()->rate();
```

## Requests Failed Count

Returns the number of requests that failed.

```php
$result->requests()->failed()->count();
```

## Requests Failed Rate

Returns the number of requests that failed per second.

```php
$result->requests()->failed()->rate();
```

## Requests Server Duration

Returns the request server duration in milliseconds.

This is the time spent waiting for the server to respond with a status code.

```php
$result->requests()->server()->duration()->avg();
                                      // ->min();
                                      // ->med();    
                                      // ->max();
                                      // ->p90();
                                      // ->p95();
```

## Requests DNS Lookup Duration

> This metric is affected by the network latency between the client and the DNS server.

Returns the request DNS lookup duration in milliseconds.

```php
$result->requests()->dnsLookup()->duration()->avg();
                                         // ->min();
                                         // ->med();   
                                         // ->max();
                                         // ->p90();
                                         // ->p95();
```

## Requests TLS Handshaking Duration

> This metric is affected by the network latency between the client and the server.

Returns the request TLS handshaking duration in milliseconds.

```php
$result->requests()->tlsHandshaking()->duration()->avg();
                                              // ->min();
                                              // ->med();   
                                              // ->max();
                                              // ->p90();
                                              // ->p95();
```

## Requests Download Duration

> This metric is affected by the network latency between the client and the server.

Returns the request download duration in milliseconds.

```php
$result->requests()->download()->duration()->avg();
                                        // ->min();
                                        // ->med();   
                                        // ->max();
                                        // ->p90();
                                        // ->p95();
```

## Requests Download Data Count

Returns the request download data count in bytes.

```php
$result->requests()->download()->data()->count();
```

## Requests Download Data Rate

Returns the request download data rate in bytes per second.

```php
$result->requests()->download()->data()->rate();
```

## Requests Upload Duration

> This metric is affected by the network latency between the client and the server.

Returns the request upload duration in milliseconds.

```php
$result->requests()->upload()->duration()->avg();
                                      // ->min();
                                      // ->med();   
                                      // ->max();
                                      // ->p90();
                                      // ->p95();
```

## Requests Upload Data Count

Returns the request upload data count in bytes.

```php
$result->requests()->upload()->data()->count();
```

## Requests Upload Data Rate

Returns the request upload data rate in bytes per second.

```php
$result->requests()->upload()->data()->rate();
```

## Test Run Concurrency

Returns the number of concurrent requests made during the stress test, which is the value you set using the `--concurrency` option or the `with()->concurrentRequests()` method.

```php

```php
$result->testRun()->concurrency();
```

## Test Run Duration

Returns the duration of the stress test, which is the value you set using the `--duration` option or the `for()->seconds()` method.

```php
$result->testRun()->duration();
```
