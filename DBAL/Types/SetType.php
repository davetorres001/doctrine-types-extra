<?php

/*
 * Copyright (c) 2015 Mihai Stancu <stancu.t.mihai@gmail.com>
 *
 * This source file is subject to the license that is bundled with this source
 * code in the LICENSE.md file.
 */

namespace MS\DoctrineTypes\DBAL\Types;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use MS\DoctrineTypes\Set;

class SetType extends EnumType
{
    const NAME = 'set';

    /**
     * @param Set              $values
     * @param AbstractPlatform $platform
     *
     * @return string|null
     */
    public function convertToDatabaseValue($values, AbstractPlatform $platform)
    {
        if (null === $values || 0 === $values  || '' === $values || array() === $values) {
            return;
        }

        if ($platform instanceof MySqlPlatform) {
            return implode(',', (array) $values->get());
        }

        return $values->get(true);
    }

    /**
     * @param string           $values
     * @param AbstractPlatform $platform
     *
     * @return array
     */
    public function convertToPHPValue($values, AbstractPlatform $platform)
    {
        if ($values === null || $values === 0 || $values === '') {
            return array();
        }

        if ($platform instanceof MySqlPlatform) {
            $values = explode(',', $values);
        }

        if (!empty($className = static::DATA_CLASS)) {
            return new $className($values);
        }

        return $values;
    }

    /**
     * @param array            $fieldDeclaration
     * @param AbstractPlatform $platform
     *
     * @throws DBALException
     *
     * @return string
     */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array();
        foreach ($this->getValues($fieldDeclaration) as $value) {
            $values[] = $platform->quoteStringLiteral($value);
        }

        if ($platform instanceof MySqlPlatform) {
            return 'SET('.implode(',', $values).')';
        }

        return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }
}
