<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Functional\Validator\Constraints;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Spiral\Filters\Exception\ValidationException;
use Spiral\PhoneNumber\Tests\App\Request\PhoneRequest;
use Spiral\PhoneNumber\Tests\Functional\TestCase;

final class PhoneNumberValidatorTest extends TestCase
{
    public function testSuccessValidation(): void
    {
        $this->getContainer()->bind(
            ServerRequestInterface::class,
            $this->createRequest(['phone' => '+1 650 253 0000', 'phone_with_custom_error' => '+16502530000'])
        );

        $filter = $this->getContainer()->get(PhoneRequest::class);

        $this->assertSame('+1 650 253 0000', $filter->phone);
        $this->assertSame('+16502530000', $filter->phoneWithCustomError);
    }

    public function testFailValidation(): void
    {
        $this->getContainer()->bind(
            ServerRequestInterface::class,
            $this->createRequest(['phone' => 'foo', 'phone_with_custom_error' => 'bar'])
        );

        $exception = null;
        try {
            $this->getContainer()->get(PhoneRequest::class);
        } catch (ValidationException $e) {
            $exception = $e;
        }

        $this->assertInstanceOf(ValidationException::class, $exception);
        $this->assertSame(['This value is not a valid phone number.'], $exception->errors['phone']);
        $this->assertSame(['foo'], $exception->errors['phone_with_custom_error']);
    }

    private function createRequest(array $data): ServerRequestInterface
    {
        $factory = $this->getContainer()->get(ServerRequestFactoryInterface::class);

        return $factory->createServerRequest('POST', '/foo')->withParsedBody($data);
    }
}
