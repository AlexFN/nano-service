# This is my package nano-service

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alexfn/nano-service.svg?style=flat-square)](https://packagist.org/packages/alexfn/nano-service)
[![Tests](https://img.shields.io/github/actions/workflow/status/alexfn/nano-service/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alexfn/nano-service/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/alexfn/nano-service.svg?style=flat-square)](https://packagist.org/packages/alexfn/nano-service)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/nano-service.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/nano-service)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require alexfn/nano-service
```

## Usage

1. Add environment variables

```bash
AMQP_PROJECT="project-name"

AMQP_HOST="rabbitmq-host"
AMQP_PORT="5672"
AMQP_USER="rmuser"
AMQP_PASS="rmpassword"
AMQP_VHOST="/"

# Required for the consumer
AMQP_MICROSERVICE="microservice-name"
```

2. Create message

```php
$message = new NanoServiceMessage(
    // Body data
    [
        'key' => 'Value',
    ],
    // Message property (Optional)
    [
        'content_type' => 'text/json',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    ]
);

$message->addPayload([
    'key1' => 'Value 1',
    'key2' => 'Value 2',
]);
```

3. Publish message

```php
$message = (new NanoServiceMessage())
    ->addPayload([
        'key' => 'Value',
    ]);

(new NanoPublisher())
    ->setMessage($message)
    ->publish('event-name');
```

4. Consume messages

```php
$consumer = new NanoConsumer();
$consumer
    ->events('event-name')
    ->consume(function (NanoServiceMessage $message) {
        $payload = $message->getPayload();// array
    });
```

## Additional message methods

```php
$message = (new NanoServiceMessage())
    ->addPayload([
        'key' => 'Value',
    ]);
```

```php
$message = (new NanoServiceMessage())
    ->addMeta([
        'key' => 'Value',
    ]);
```

```php
$message->getPayload();
$message->getPayloadAttribute('key');
$message->getPayloadAttribute('key', 'default_value');

$message->getMeta();
$message->getMetaAttribute('key');
$message->getMetaAttribute('key', 'default_value');

$message->getStatusCode(); // Default 'unknown'
$message->setStatusCode('success');
$message->getStatusData(); // Default []
$message->setStatusData([]);
```

### Replace attributes
```php
$message = (new NanoServiceMessage())
    ->addPayload([
        'key1' => 'Value 1',
        'key2' => 'Value 2',
    ])
    ->addPayload([
        'key1' => 'New value 1',
        'key3' => 'New value 3',
    ]);

// Result: {"key1":"Value 1","key2":"Value 2","key3":"New value 3"}
```

```php
$message = (new NanoServiceMessage())
    ->addPayload([
        'key1' => 'Value 1',
        'key2' => 'Value 2',
    ])
    ->addPayload(
        [
            'key1' => 'New value 1',
            'key3' => 'New value 3',
        ],
        true
    );

// Result: {"key1":"New value 1","key2":"Value 2","key3":"New value 3"}
```

```php
$message = (new NanoServiceMessage())
    ->setStatusData([
        'key1' => 'Value 1',
        'key2' => 'Value 2',
    ])
    ->setStatusData(
        [
            'key1' => 'New value 1',
            'key3' => 'New value 3',
        ],
        true
    );

// Result: {"key1":"New value 1","key3":"New value 3"}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alexander O.](https://github.com/AlexFN)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
