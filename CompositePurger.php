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
    public function purge(array $context): PurgerInterface
    {
        foreach ($this->purgers as $purger) {
            $this->proceedPurge($purger, $context);
        }

        return $this;
    }

    /**
     * @param PurgerInterface $purger
     * @param array           $context
     *
     * @return CompositePurger
     */
    private function proceedPurge(PurgerInterface $purger, array $context): self
    {
        $purger->purge($context);

        return $this;
    }
}
