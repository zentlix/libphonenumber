<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Unit\Twig\Extension;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\TestCase;
use Spiral\PhoneNumber\Exception\InvalidArgumentException;
use Spiral\PhoneNumber\Twig\Extension\PhoneNumberExtension;

final class PhoneNumberExtensionTest extends TestCase
{
    public function testGetFilters(): void
    {
        $extension = new PhoneNumberExtension(PhoneNumberUtil::getInstance());

        $filters = $extension->getFilters();

        $this->assertCount(2, $filters);
        $this->assertSame('phone_number_format', $filters[0]->getName());
        $this->assertSame('phone_number_format_out_of_country_calling_number', $filters[1]->getName());
    }

    public function testGetTests(): void
    {
        $extension = new PhoneNumberExtension(PhoneNumberUtil::getInstance());

        $tests = $extension->getTests();

        $this->assertCount(1, $tests);
        $this->assertSame('phone_number_of_type', $tests[0]->getName());
    }

    public function testFormat(): void
    {
        $util = PhoneNumberUtil::getInstance();
        $extension = new PhoneNumberExtension($util);

        $this->assertSame(
            '+1 650-253-0000',
            $extension->format($util->parse('+16502530000'), PhoneNumberFormat::INTERNATIONAL)
        );
        $this->assertSame(
            '(650) 253-0000',
            $extension->format($util->parse('+16502530000'), PhoneNumberFormat::NATIONAL)
        );
        $this->assertSame(
            '+16502530000',
            $extension->format($util->parse('+1 650-253-0000'), 'E164')
        );
    }

    public function testFormatException(): void
    {
        $util = PhoneNumberUtil::getInstance();
        $extension = new PhoneNumberExtension($util);

        $this->expectException(InvalidArgumentException::class);
        $extension->format($util->parse('+16502530000'), 'foo');
    }

    public function testFormatOutOfCountryCallingNumber(): void
    {
        $phoneNumber = (PhoneNumberUtil::getInstance())->parse('+16502530000');

        $util = $this->createMock(PhoneNumberUtil::class);
        $util
            ->expects($this->once())
            ->method('formatOutOfCountryCallingNumber')
            ->with($phoneNumber, 'US')
            ->willReturn('foo');

        $extension = new PhoneNumberExtension($util);

        $extension->formatOutOfCountryCallingNumber($phoneNumber, 'US');
    }

    public function testIsType(): void
    {
        $util = PhoneNumberUtil::getInstance();
        $extension = new PhoneNumberExtension($util);

        $gbMobile = new PhoneNumber();
        $gbMobile->setCountryCode(44)->setNationalNumber(7912345678);

        $this->assertTrue($extension->isType($gbMobile, PhoneNumberType::MOBILE));
        $this->assertFalse($extension->isType($gbMobile, PhoneNumberType::FIXED_LINE));
        $this->assertTrue($extension->isType($gbMobile, 'MOBILE'));
        $this->assertFalse($extension->isType($gbMobile, 'FIXED_LINE'));
    }

    public function testIsTypeException(): void
    {
        $util = PhoneNumberUtil::getInstance();
        $extension = new PhoneNumberExtension($util);

        $this->expectException(InvalidArgumentException::class);
        $extension->isType($util->parse('+16502530000'), 'foo');
    }
}
