<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Unit\Validator\Constraints;

use PHPUnit\Framework\TestCase;
use Spiral\PhoneNumber\Exception\InvalidArgumentException;
use Spiral\PhoneNumber\Validator\Constraints\PhoneNumber;

final class PhoneNumberTest extends TestCase
{
    public function testGetTypes(): void
    {
        $constraint = new PhoneNumber(type: PhoneNumber::MOBILE);
        $this->assertSame([PhoneNumber::MOBILE], $constraint->getTypes());

        $constraint = new PhoneNumber(type: [PhoneNumber::MOBILE, PhoneNumber::FIXED_LINE]);
        $this->assertSame([PhoneNumber::MOBILE, PhoneNumber::FIXED_LINE], $constraint->getTypes());
    }

    /**
     * @dataProvider typeMessagesDataProvider
     */
    public function testGetMessage(string|array $type, string $expected): void
    {
        $constraint = new PhoneNumber(type: $type);
        $this->assertSame($expected, $constraint->getMessage());
    }

    public function testGetCustomMessage(): void
    {
        $constraint = new PhoneNumber(message: 'foo');
        $this->assertSame('foo', $constraint->getMessage());
    }

    /**
     * @dataProvider typeNamesDataProvider
     */
    public function testGetTypeNames(string|array $type, array $expected): void
    {
        $constraint = new PhoneNumber(type: $type);
        $this->assertSame($expected, $constraint->getTypeNames());
    }

    public function testGetTypeNameException(): void
    {
        $constraint = new PhoneNumber(type: 'foo');

        $this->expectException(InvalidArgumentException::class);
        $constraint->getTypeNames();
    }

    public function typeMessagesDataProvider(): \Traversable
    {
        yield [PhoneNumber::ANY, 'This value is not a valid phone number.'];
        yield [PhoneNumber::FIXED_LINE, 'This value is not a valid fixed-line number.'];
        yield [PhoneNumber::MOBILE, 'This value is not a valid mobile number.'];
        yield [PhoneNumber::PAGER, 'This value is not a valid pager number.'];
        yield [PhoneNumber::PERSONAL_NUMBER, 'This value is not a valid personal number.'];
        yield [PhoneNumber::PREMIUM_RATE, 'This value is not a valid premium-rate number.'];
        yield [PhoneNumber::SHARED_COST, 'This value is not a valid shared-cost number.'];
        yield [PhoneNumber::TOLL_FREE, 'This value is not a valid toll-free number.'];
        yield [PhoneNumber::UAN, 'This value is not a valid UAN.'];
        yield [PhoneNumber::VOIP, 'This value is not a valid VoIP number.'];
        yield [PhoneNumber::VOICEMAIL, 'This value is not a valid voicemail access number.'];
        yield [[PhoneNumber::MOBILE, PhoneNumber::FIXED_LINE], 'This value is not a valid phone number.'];
    }

    public function typeNamesDataProvider(): \Traversable
    {
        yield [PhoneNumber::ANY, ['phone number']];
        yield [PhoneNumber::FIXED_LINE, ['fixed-line number']];
        yield [PhoneNumber::MOBILE, ['mobile number']];
        yield [PhoneNumber::PAGER, ['pager number']];
        yield [PhoneNumber::PERSONAL_NUMBER, ['personal number']];
        yield [PhoneNumber::PREMIUM_RATE, ['premium-rate number']];
        yield [PhoneNumber::SHARED_COST, ['shared-cost number']];
        yield [PhoneNumber::TOLL_FREE, ['toll-free number']];
        yield [PhoneNumber::UAN, ['UAN']];
        yield [PhoneNumber::VOIP, ['VoIP number']];
        yield [PhoneNumber::VOICEMAIL, ['voicemail access number']];
        yield [[PhoneNumber::MOBILE, PhoneNumber::FIXED_LINE], ['mobile number', 'fixed-line number']];
    }
}
