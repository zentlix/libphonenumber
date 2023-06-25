<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Validator\Constraints;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber as PhoneNumberObject;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PhoneNumberValidator extends ConstraintValidator
{
    private PhoneNumberUtil $phoneUtil;
    private ?PropertyAccessorInterface $propertyAccessor = null;

    public function __construct(
        PhoneNumberUtil $phoneUtil = null,
        private readonly string $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION,
        private readonly int $format = PhoneNumberFormat::INTERNATIONAL
    ) {
        $this->phoneUtil = $phoneUtil ?? PhoneNumberUtil::getInstance();
    }

    /**
     * @param PhoneNumber $constraint
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (false === $value instanceof PhoneNumberObject) {
            $value = (string) $value;

            try {
                $phoneNumber = $this->phoneUtil->parse($value, $this->getRegion($constraint));
            } catch (NumberParseException) {
                $this->addViolation($value, $constraint);

                return;
            }
        } else {
            $phoneNumber = $value;
            $value = $this->phoneUtil->format($phoneNumber, $constraint->format ?? $this->format);
        }

        if (false === $this->phoneUtil->isValidNumber($phoneNumber)) {
            $this->addViolation($value, $constraint);

            return;
        }

        $validTypes = [];
        foreach ($constraint->getTypes() as $type) {
            switch ($type) {
                case PhoneNumber::FIXED_LINE:
                    $validTypes[] = PhoneNumberType::FIXED_LINE;
                    $validTypes[] = PhoneNumberType::FIXED_LINE_OR_MOBILE;
                    break;
                case PhoneNumber::MOBILE:
                    $validTypes[] = PhoneNumberType::MOBILE;
                    $validTypes[] = PhoneNumberType::FIXED_LINE_OR_MOBILE;
                    break;
                case PhoneNumber::PAGER:
                    $validTypes[] = PhoneNumberType::PAGER;
                    break;
                case PhoneNumber::PERSONAL_NUMBER:
                    $validTypes[] = PhoneNumberType::PERSONAL_NUMBER;
                    break;
                case PhoneNumber::PREMIUM_RATE:
                    $validTypes[] = PhoneNumberType::PREMIUM_RATE;
                    break;
                case PhoneNumber::SHARED_COST:
                    $validTypes[] = PhoneNumberType::SHARED_COST;
                    break;
                case PhoneNumber::TOLL_FREE:
                    $validTypes[] = PhoneNumberType::TOLL_FREE;
                    break;
                case PhoneNumber::UAN:
                    $validTypes[] = PhoneNumberType::UAN;
                    break;
                case PhoneNumber::VOIP:
                    $validTypes[] = PhoneNumberType::VOIP;
                    break;
                case PhoneNumber::VOICEMAIL:
                    $validTypes[] = PhoneNumberType::VOICEMAIL;
                    break;
            }
        }

        $validTypes = array_unique($validTypes);

        if (0 < \count($validTypes)) {
            $type = $this->phoneUtil->getNumberType($phoneNumber);

            if (!\in_array($type, $validTypes, true)) {
                $this->addViolation($value, $constraint);
            }
        }
    }

    private function getRegion(PhoneNumber $constraint): ?string
    {
        $defaultRegion = null;
        if (null !== $path = $constraint->regionPath) {
            $object = $this->context->getObject();
            if (null === $object) {
                throw new \LogicException('The current validation does not concern an object');
            }

            try {
                $defaultRegion = $this->getPropertyAccessor()->getValue($object, $path);
            } catch (NoSuchPropertyException $e) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        'Invalid property path `%s` provided to `%s` constraint: ',
                        $path,
                        get_debug_type($constraint)
                    ).$e->getMessage(),
                    0,
                    $e
                );
            }
        }

        return $defaultRegion ?? $constraint->defaultRegion ?? $this->defaultRegion;
    }

    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor): void
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (null === $this->propertyAccessor) {
            if (!class_exists(PropertyAccess::class)) {
                throw new LogicException(
                    'Unable to use property path as the Symfony PropertyAccess component is not installed.'
                );
            }
            $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->propertyAccessor;
    }

    private function addViolation(mixed $value, PhoneNumber $constraint): void
    {
        $this->context->buildViolation($constraint->getMessage())
            ->setParameter('{{ types }}', implode(', ', $constraint->getTypeNames()))
            ->setParameter('{{ value }}', $this->formatValue($value))
            ->setCode(PhoneNumber::INVALID_PHONE_NUMBER_ERROR)
            ->addViolation();
    }
}
