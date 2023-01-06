# Google libphonenumber integration package for Spiral Framework

[![PHP Version Require](https://poser.pugx.org/zentlix/libphonenumber/require/php)](https://packagist.org/packages/zentlix/libphonenumber)
[![Latest Stable Version](https://poser.pugx.org/zentlix/libphonenumber/v/stable)](https://packagist.org/packages/zentlix/libphonenumber)
[![phpunit](https://github.com/zentlix/libphonenumber/actions/workflows/phpunit.yml/badge.svg)](https://github.com/zentlix/libphonenumber/actions)
[![psalm](https://github.com/zentlix/libphonenumber/actions/workflows/psalm.yml/badge.svg)](https://github.com/zentlix/libphonenumber/actions)
[![Codecov](https://codecov.io/gh/zentlix/libphonenumber/branch/master/graph/badge.svg)](https://codecov.io/gh/zentlix/libphonenumber)
[![Total Downloads](https://poser.pugx.org/zentlix/libphonenumber/downloads)](https://packagist.org/packages/zentlix/libphonenumber)
[![type-coverage](https://shepherd.dev/github/zentlix/libphonenumber/coverage.svg)](https://shepherd.dev/github/zentlix/libphonenumber)
[![psalm-level](https://shepherd.dev/github/zentlix/libphonenumber/level.svg)](https://shepherd.dev/github/zentlix/libphonenumber)

The package provides tools for parsing, formatting, and validating international phone numbers
in `Spiral Framework`. It integrates the `Google libphonenumber` library,
which is a powerful and widely used library for working with phone numbers.

## Requirements

Make sure that your server is configured with following PHP version and extensions:

- PHP 8.1+
- Spiral framework 3.5+

## Installation

To install the package, use Composer by running the following command:

```bash
composer require zentlix/libphonenumber
```

To enable the package in your Spiral Framework application, you will need to add the
`Spiral\PhoneNumber\Bootloader\PhoneNumberBootloader` class to the list of bootloaders in your application:

```php
protected const LOAD = [
    // ...
    \Spiral\PhoneNumber\Bootloader\PhoneNumberBootloader::class,
];
```

> **Note**
> If you are using [`spiral-packages/discoverer`](https://github.com/spiral-packages/discoverer),
> you don't need to register bootloader by yourself.

## Configuration

The configuration file should be located at `app/config/libphonenumber.php`, and it allows you to set options
such as the default region and default format for phone numbers.

Here is an example of how the configuration file might look:

```php
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

return [
    'default_region' => PhoneNumberUtil::UNKNOWN_REGION,
    'default_format' => PhoneNumberFormat::E164,
];
```

The `default_region` option specifies the default region code to use when parsing and formatting phone numbers.
This can be set to any valid region code, such as `US`, `GB`, and others.

The `default_format` option specifies the default format to use when formatting phone numbers.
This can be set to any of the constants provided by the `libphonenumber\PhoneNumberFormat` class,
such as `NATIONAL`, `INTERNATIONAL`, or `E164`.

## Usage

The `libphonenumber\PhoneNumberUtil` class provides methods for parsing phone numbers from strings,
formatting phone numbers as strings, and validating phone numbers.

The `libphonenumber\PhoneNumber` class is a class that represents a phone number in an object-oriented format.
It is returned by the `parse` method of the `libphonenumber\PhoneNumberUtil` class, and it stores complete
phone number information such as the country code, national number, and other details.

One way to use the `PhoneNumberUtil` class is to inject it into your classes using dependency injection. For example:

```php

use libphonenumber\PhoneNumberUtil;

final class SomeService
{
     public function __construct(
         private readonly PhoneNumberUtil $phoneNumberUtil
     ) {
     }

     public function do(): void
     {
         $phoneNumber = $this->phoneNumberUtil->parse('+1 650 253 0000', 'US');

         // ...
     }
}
```

Alternatively, you can create a `libphonenumber\PhoneNumberUtil` instance manually by calling the
`PhoneNumberUtil::getInstance` method. For example:

```php
$utils = PhoneNumberUtil::getInstance();
$phoneNumber = $utils->parse('+1 650 253 0000', 'US');
```

Here is an example of how you might use some of the methods provided
by the `libphonenumber\PhoneNumberUtil` and `libphonenumber\PhoneNumber` classes:

```php
$utils = libphonenumber\PhoneNumberUtil::getInstance();

$phoneNumber = $utils->parse('+1 650 253 0000', 'US');
$phoneNumber->getCountryCode(); // 1
$phoneNumber->getNationalNumber(); // 6502530000

$utils->format($phoneNumber, PhoneNumberFormat::E164); // +16502530000
$utils->format($phoneNumber, PhoneNumberFormat::INTERNATIONAL); // +1 650-253-0000
$utils->format($phoneNumber, PhoneNumberFormat::NATIONAL); // (650) 253-0000
$utils->format($phoneNumber, PhoneNumberFormat::RFC3966); // tel:+1-650-253-0000
```

## Validation

The package provides a `Spiral\PhoneNumber\Validator\Constraints\PhoneNumber` constraint that can be used to validate
phone numbers using the `spiral-packages/symfony-validator` component.

To use the `Spiral\PhoneNumber\Validator\Constraints\PhoneNumber` constraint, you will first need to make sure that
the `spiral-packages/symfony-validator` package is installed and enabled in your Spiral Framework application.

Once the `spiral-packages/symfony-validator` package is installed and enabled, you can use the `PhoneNumber`
constraint in your code like this:

```php
use libphonenumber\PhoneNumber;
use Spiral\PhoneNumber\Validator\Constraints;

class User
{
    #[Constraints\PhoneNumber]
    protected ?PhoneNumber $phone = null;
}
```

In this example, the `PhoneNumber` constraint is applied to the **$phone** property of the **User** class using the
attribute. This will cause the Validator to validate the **$phone** property as a phone number when
the User object is validated. If the value of the $phone property is not a valid phone number, the validation will fail.

You can also specify additional options when using the `PhoneNumber` constraint:

```php
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use Spiral\PhoneNumber\Validator\Constraints;

class User
{
    #[Constraints\PhoneNumber(
        format: PhoneNumberFormat::INTERNATIONAL,
        defaultRegion: 'US',
        message: 'The phone number is invalid!'
    )]
    protected ?PhoneNumber $phone = null;
}
```

## Serialization

The package provides a `Spiral\PhoneNumber\Serializer\Normalizer\PhoneNumberNormalizer` class that can be used
to serialize and deserialize `libphonenumber\PhoneNumber` objects using the `spiral-packages/symfony-serializer` package.

Once the `spiral-packages/symfony-serializer` package is installed and enabled, the `PhoneNumberNormalizer` class
will be automatically registered as a normalizer for `libphonenumber\PhoneNumber` objects.
This means that you can use the Symfony Serializer to serialize and deserialize `libphonenumber\PhoneNumber`
objects just like any other object:

```php
$utils = \libphonenumber\PhoneNumberUtil::getInstance();

/** @var \Spiral\Serializer\SerializerManager $manager */
$manager = $this->getContainer()->get(\Spiral\Serializer\SerializerManager::class);

$result = $manager->getSerializer('json')->serialize($utils->parse('+1 650 253 0000', 'US'));

echo $result; // "+16502530000"

$phoneNumber = $manager->getSerializer('json')->unserialize(json_encode('+16502530000'), PhoneNumber::class);

var_dump(get_debug_type($phoneNumber)); // libphonenumber\PhoneNumber
```

## Twig

The package provides a `Spiral\PhoneNumber\Twig\Extension\PhoneNumberExtension` class that can be used
to add `filters` and `test` to the Twig templating engine.

To use the `PhoneNumberExtension` class, you will first need to make sure that the `spiral/twig-bridge` package
is installed and enabled in your Spiral Framework application.

Once the `spiral/twig-bridge` package is installed and enabled, the `PhoneNumberExtension` class will be automatically
registered as an extension for Twig.

The PhoneNumberExtension class provides the following filters:

- `phone_number_format`: Formats a phone number for out-of-country dialing purposes.
- `phone_number_format_out_of_country_calling_number` : Formats a `libphonenumber\Phone

```php
{{ phoneNumber | phone_number_format('NATIONAL') }}
```

## Testing

```bash
composer test
```

```bash
composer psalm
```

```bash
composer cs
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
