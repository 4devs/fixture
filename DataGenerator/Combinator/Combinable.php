<?php

namespace FDevs\Fixture\DataGenerator\Combinator;

class Combinable implements CombinableInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @var null|OptionsModifierInterface
     */
    private $modifier;

    /**
     * @param string $type
     *
     * @return Combinable
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param array $options
     *
     * @return Combinable
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionsModifier(): ?OptionsModifierInterface
    {
        return $this->modifier;
    }

    /**
     * @param OptionsModifierInterface|null $modifier
     *
     * @return Combinable
     */
    public function setOptionsModifier(?OptionsModifierInterface $modifier): self
    {
        $this->modifier = $modifier;

        return $this;
    }
}
