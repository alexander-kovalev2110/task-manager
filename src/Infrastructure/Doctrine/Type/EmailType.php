<?php

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\User\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmailType extends Type
{
    public const NAME = 'email';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL([
            'length' => 180,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Email
    {
        if ($value === null) {
            return null;
        }

        return new Email($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Email) {
            throw new \InvalidArgumentException('Expected Email object');
        }

        return $value->value();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}