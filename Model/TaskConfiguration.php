<?php

namespace Comparon\MegacronBundle\Model;

class TaskConfiguration
{
    /** @var string */
    private $cronExpression;

    /**
     * Indicates if the command should overlap itself.
     *
     * @var bool
     */
    private $withOverlapping = true;

    /** @var string[] */
    private $parameters = [];

    public function isWithOverlapping(): bool
    {
        return $this->withOverlapping;
    }

    public function setWithOverlapping(bool $withOverlapping): self
    {
        $this->withOverlapping = $withOverlapping;
        return $this;
    }

    public function getCronExpression(): ?string
    {
        return $this->cronExpression;
    }

    public function setCronExpression(string $cronExpression): self
    {
        $this->cronExpression = $cronExpression;
        return $this;
    }

    public function addParameter(string $parameter): self
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
