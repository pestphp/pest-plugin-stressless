<?php

declare(strict_types=1);

use function Pest\Stressless\stress;

stress('correctly use get method by default', function (): void {
    expect($this->stress->method())->toBe('get');
});

stress('correctly sets the delete method', function (): void {
    $this->stress->delete();

    expect($this->stress->method())->toBe('delete');
});

stress('correctly sets the get method', function (): void {
    $this->stress->get();

    expect($this->stress->method())->toBe('get');
});

stress('correctly sets the head method', function (): void {
    $this->stress->head();

    expect($this->stress->method())->toBe('head');
});

stress('correctly sets the options method without payload', function (): void {
    $this->stress->options();

    expect($this->stress->method())->toBe('options')
        ->and($this->stress->payload())->toBe([]);
});

stress('correctly sets the options method with payload', function (): void {
    $this->stress->options(['foo' => 'bar']);

    expect($this->stress->method())->toBe('options')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

stress('correctly sets the patch method without payload', function (): void {
    $this->stress->patch();

    expect($this->stress->method())->toBe('patch')
        ->and($this->stress->payload())->toBe([]);
});

stress('correctly sets the patch method with payload', function (): void {
    $this->stress->patch(['foo' => 'bar']);

    expect($this->stress->method())->toBe('patch')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

stress('correctly sets the put method without payload', function (): void {
    $this->stress->put();

    expect($this->stress->method())->toBe('put')
        ->and($this->stress->payload())->toBe([]);
});

stress('correctly sets the put method with payload', function (): void {
    $this->stress->put(['foo' => 'bar']);

    expect($this->stress->method())->toBe('put')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

stress('correctly sets the post method', function (): void {
    $this->stress->post(['foo' => 'bar']);

    expect($this->stress->method())->toBe('post')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});
