<?php

declare(strict_types=1);

it('correctly use get method by default', function () {
    expect($this->stress->method())->toBe('get');
});

it('correctly sets the delete method', function () {
    $this->stress->delete();

    expect($this->stress->method())->toBe('delete');
});

it('correctly sets the get method', function () {
    $this->stress->get();

    expect($this->stress->method())->toBe('get');
});

it('correctly sets the head method', function () {
    $this->stress->head();

    expect($this->stress->method())->toBe('head');
});

it('correctly sets the options method without payload', function () {
    $this->stress->options();

    expect($this->stress->method())->toBe('options')
        ->and($this->stress->payload())->toBe([]);
});

it('correctly sets the options method with payload', function () {
    $this->stress->options(['foo' => 'bar']);

    expect($this->stress->method())->toBe('options')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

it('correctly sets the patch method without payload', function () {
    $this->stress->patch();

    expect($this->stress->method())->toBe('patch')
        ->and($this->stress->payload())->toBe([]);
});

it('correctly sets the patch method with payload', function () {
    $this->stress->patch(['foo' => 'bar']);

    expect($this->stress->method())->toBe('patch')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

it('correctly sets the put method without payload', function () {
    $this->stress->put();

    expect($this->stress->method())->toBe('put')
        ->and($this->stress->payload())->toBe([]);
});

it('correctly sets the put method with payload', function () {
    $this->stress->put(['foo' => 'bar']);

    expect($this->stress->method())->toBe('put')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});

it('correctly sets the post method', function () {
    $this->stress->post(['foo' => 'bar']);

    expect($this->stress->method())->toBe('post')
        ->and($this->stress->payload())->toBe(['foo' => 'bar']);
});
