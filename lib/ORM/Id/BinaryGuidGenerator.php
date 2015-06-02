<?php

namespace MS\Doctrine\ORM\Id;

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;
use Doctrine\ORM\Mapping\Entity;

class BinaryGuidGenerator extends UuidGenerator
{
    /**
     * @param EntityManager $manager
     * @param Entity        $entity
     *
     * @return string
     */
    public function generate(EntityManager $manager, $entity)
    {
        $connection = $manager->getConnection();
        $platform   = $connection->getDatabasePlatform();

        if ($platform InstanceOf MySqlPlatform) {
            return $connection->query('SELECT UUID()')->fetchColumn(0);
        } else {
            return parent::generate($manager, $entity);
        }
    }
}
