<?php

namespace App\Doctrine\Type;

use App\Entity\EtatPanier;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EtatPanierType extends Type  // Must extend Doctrine's Type class
{
    public const NAME = 'etat_panier';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Typically for enum-like types, we use a string type with a specific length
        // You might want to adjust the length based on your actual enum values
        return $platform->getVarcharTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?EtatPanier
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof EtatPanier) {
            return $value;
        }

        return EtatPanier::from($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof EtatPanier) {
            return $value->value;
        }

        // Additional validation if needed
        EtatPanier::from($value); // This will throw if invalid
        return (string) $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    // Optional: Add requiresSQLCommentHint if you want Doctrine to always add comments
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}