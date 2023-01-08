<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Validator\Constraints;

use Spiral\Attributes\NamedArgumentConstructor;
use Spiral\PhoneNumber\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Constraint;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY), NamedArgumentConstructor]
final class PhoneNumber extends Constraint
{
    public const ANY = 'any';
    public const FIXED_LINE = 'fixed_line';
    public const MOBILE = 'mobile';
    public const PAGER = 'pager';
    public const PERSONAL_NUMBER = 'personal_number';
    public const PREMIUM_RATE = 'premium_rate';
    public const SHARED_COST = 'shared_cost';
    public const TOLL_FREE = 'toll_free';
    public const UAN = 'uan';
    public const VOIP = 'voip';
    public const VOICEMAIL = 'voicemail';

    public const INVALID_PHONE_NUMBER_ERROR = 'ca23g4ca-38f4-4325-9bcc-eb570a4ave7f';

    protected const ERROR_NAMES = [
        self::INVALID_PHONE_NUMBER_ERROR => 'INVALID_PHONE_NUMBER_ERROR',
    ];

    public function __construct(
        public readonly ?int $format = null,
        public readonly string|array $type = self::ANY,
        public readonly ?string $defaultRegion = null,
        public readonly ?string $regionPath = null,
        public readonly ?string $message = null,
        array $groups = null,
        mixed $payload = null,
        array $options = []
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTypes(): array
    {
        if (\is_array($this->type)) {
            return $this->type;
        }

        return [$this->type];
    }

    public function getMessage(): string
    {
        if (null !== $this->message) {
            return $this->message;
        }

        $types = $this->getTypes();
        if (1 === \count($types)) {
            $typeName = $this->getTypeName($types[0]);

            return "This value is not a valid $typeName.";
        }

        return 'This value is not a valid phone number.';
    }

    public function getTypeNames(): array
    {
        $types = \is_array($this->type) ? $this->type : [$this->type];

        $typeNames = [];
        foreach ($types as $type) {
            $typeNames[] = $this->getTypeName($type);
        }

        return $typeNames;
    }

    private function getTypeName(string $type): string
    {
        return match ($type) {
            self::FIXED_LINE => 'fixed-line number',
            self::MOBILE => 'mobile number',
            self::PAGER => 'pager number',
            self::PERSONAL_NUMBER => 'personal number',
            self::PREMIUM_RATE => 'premium-rate number',
            self::SHARED_COST => 'shared-cost number',
            self::TOLL_FREE => 'toll-free number',
            self::UAN => 'UAN',
            self::VOIP => 'VoIP number',
            self::VOICEMAIL => 'voicemail access number',
            self::ANY => 'phone number',
            default => throw new InvalidArgumentException(sprintf('Unknown phone number type `%s`.', $type))
        };
    }
}
