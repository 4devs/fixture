<?php

namespace FDevs\Fixture\Adapter\Doctrine;

use Doctrine\Common\DataFixtures\Purger\ORMPurger as DoctrinePurger;
use Doctrine\ORM\EntityManagerInterface;
use FDevs\Fixture\Command\ContextHandler\PurgeHandler;
use FDevs\Fixture\PurgerInterface;

class ORMPurger implements PurgerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var array
     */
    private $exclude;

    /**
     * Purger constructor.
     *
     * @param EntityManagerInterface $em
     * @param array                  $exclude
     */
    public function __construct(EntityManagerInterface $em, array $exclude = [])
    {
        $this->em = $em;
        $this->exclude = $exclude;
    }

    /**
     * {@inheritdoc}
     */
    public function purge(array $context): PurgerInterface
    {
        $purger = $this->createDoctrinePurger();
        $mode = $context[PurgeHandler::OPTION_PURGE_WITH_TRUNCATE]
            ? DoctrinePurger::PURGE_MODE_TRUNCATE
            : DoctrinePurger::PURGE_MODE_DELETE
        ;
        $purger->setPurgeMode($mode);
        $purger->purge();

        return $this;
    }

    /**
     * @return DoctrinePurger
     */
    private function createDoctrinePurger(): DoctrinePurger
    {
        return new DoctrinePurger($this->em, $this->exclude);
    }
}
