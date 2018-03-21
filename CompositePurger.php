<?php

namespace FDevs\Fixture;

class CompositePurger implements PurgerInterface
{
    /**
     * @var iterable
     */
    private $purgers;

    /**
     * CompositePurger constructor.
     *
     * @param iterable $purgers
     */
    public function __construct(iterable $purgers)
    {
        $this->purgers = $purgers;
    }


    /**
     * @inheritDoc
     */
    public function purge(): PurgerInterface
    {
        foreach ($this->purgers as $purger) {
            $this->proceedPurge($purger);
        }

        return $this;
    }

    /**
     * @param PurgerInterface $purger
     *
     * @return CompositePurger
     */
    private function proceedPurge(PurgerInterface $purger): self
    {
        $purger->purge();

        return $this;
    }
}
