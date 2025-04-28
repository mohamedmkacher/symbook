<?php
// src/Doctrine/Type/UserRoleType.php
namespace App\Doctrine\Type;

use App\Entity\UserRole;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UserRoleType extends Type
{
    public const NAME = 'user_role';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?UserRole
    {
        return $value !== null ? UserRole::from($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof UserRole ? $value->value : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}