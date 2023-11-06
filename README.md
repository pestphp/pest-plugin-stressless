This repository contains the Pest Plugin Stresless.

> If you want to start testing your application with Pest, visit the main **[Pest Repository](https://github.com/pestphp/pest)**.

- Explore our docs at **[pestphp.com »](https://pestphp.com)**
- Follow us on Twitter at **[@pestphp »](https://twitter.com/pestphp)**
- Join us at **[discord.gg/kaHY6p54JH »](https://discord.gg/kaHY6p54JH)** or **[t.me/+kYH5G4d5MV83ODk0 »](https://t.me/+kYH5G4d5MV83ODk0)**

Pest is an open-sourced software licensed under the **[MIT license](https://opensource.org/licenses/MIT)**.

---

# Stress Testing

<p align="center">
    <img src="https://pestphp.com/assets/img/stressless-banner.png" width="600" alt="PEST">
</p>

**Source code**: [github.com/pestphp/pest-plugin-stressless](https://github.com/pestphp/pest-plugin-stressless)

Stress Testing is a type of testing that inspects the stability and reliability of your application under realistic or extreme conditions — depending on the scenario you setup. For example, you can use stress testing to verify that your application can handle a large number of requests or that it can handle a large amount of data.

In Pest, you can combine the power of Stress Testing with the Expectation API ensuring no stability and reliability regressions over time. This can be useful to verify that your application is stable and reliable after a new release, or after a new deployment.

To start using Pest's Stress Testing plugin (mostly known as Stressless), you need to require the plugin via Composer.

```bash
composer require pestphp/pest-plugin-stressless --dev
```

After requiring the plugin, you may start using it in two different ways:

- Using [the `stress` command](#the-stress-command): It's useful when you want to quickly stress test a URL, without setting expectations on the result.
- Using [the `stress()` function](#the-stress-function): It's useful when you want to stress test a URL and set expectations on the result.

## The `stress` command

The `stress` command is useful when you want to quickly stress test a URL, analyze the result, and all without setting expectations on the result. It's the quickest way to launch a stress test, and it happens directly on the terminal.

To get started, you may use the `stress` command and provide the URL you wish to stress test:

```bash
./vendor/bin/pest stress example.com
```

By default, the stress test duration will be `10` seconds. However, you may customize this value using the `--duration` option:

```bash
./vendor/bin/pest stress example.com --duration=5
```

In addition, the number of concurrent requests will be `1`. However, you may also customize this value using the `--concurrency` option:

```bash
./vendor/bin/pest stress example.com --concurrency=5
```

The concurrency value represents the number of concurrent requests that will be made to the given URL. For example, if you set the concurrency to `5`, Pest will **constantly make 5 concurrent requests** to the given URL until the stress test duration is reached.

You may want to be mindful of the number of concurrent requests you configure. If you configure too many concurrent requests, you may overwhelm your application, server or hit rate limits / firewalls.

Once the stress test is completed, Pest will display a summary of the stress test result, which includes the following metrics:

<p align="center">
    <img src="https://pestphp.com/assets/img/stressless-results.png" width="600" alt="PEST">
</p>

## The `stress()` function

Once you start understanding how stress testing works, you may want to start setting expectations on the stress test result. For example, you may want to verify that the average response time is *always* less than 100ms, and this is where the `stress()` function comes in.

To get started, simply create a regular test and use the `stress()` function to stress test a given URL:

```php
<?php

use function Pest\Stressless\stress;

it('has a fast response time', function () {
    $result = stress('example.com');

    expect($result->requests()->duration()->avg())->toBeLessThan(100); // < 100.00ms
});
```

By default, the stress test duration will be 10 seconds. However, you may customize this value using the `for()->seconds()` method:

```php
$result = stress('example.com')->for(5)->seconds();
```

In addition, the number of concurrent requests will be 1. However, you may also customize this value using the `with()->concurrentRequests()` method:

```php
$result = stress('example.com')->with(5)->concurrentRequests()->for(5)->seconds();
```

At any time, you may `dd` the stress test result to see all the available metrics:

```php
$result = stress('example.com')->dd();
                             //->dump();
```


The `stress()` function return the stress test result, which you can use to set expectations. Here is the list of available methods:

### Request Duration

Returns the overall request duration in milliseconds.

```php
$result->requests()->duration()->avg();
                            // ->min();
                            // ->med();
                            // ->max();
                            // ->p90();
                            // ->p95();
```

### Requests Count

Returns the number of requests made.

```php
$result->requests()->count();
```

### Requests Rate

Returns the number of requests made per second.

```php
$result->requests()->rate();
```

### Requests Failed Count

Returns the number of requests that failed.

```php
$result->requests()->failed()->count();
```

### Requests Failed Rate

Returns the number of requests that failed per second.

```php
$result->requests()->failed()->rate();
```

### Requests Server Duration

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

### Requests DNS Lookup Duration

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

### Requests TLS Handshaking Duration

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

### Requests Download Duration

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

#### Requests Download Data Count

Returns the request download data count in bytes.

```php
$result->requests()->download()->data()->count();
```

### Requests Download Data Rate

Returns the request download data rate in bytes per second.

```php
$result->requests()->download()->data()->rate();
```

### Requests Upload Duration

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

### Requests Upload Data Count

Returns the request upload data count in bytes.

```php
$result->requests()->upload()->data()->count();
```

### Requests Upload Data Rate

Returns the request upload data rate in bytes per second.

```php
$result->requests()->upload()->data()->rate();
```

### Test Run Concurrency

Returns the number of concurrent requests made during the stress test, which is the value you set using the `--concurrency` option or the `with()->concurrentRequests()` method.

```php

```php
$result->testRun()->concurrency();
```

### Test Run Duration

Returns the duration of the stress test, which is the value you set using the `--duration` option or the `for()->seconds()` method.

```php
$result->testRun()->duration();
```
