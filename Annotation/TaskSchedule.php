<?php

namespace Comparon\SchedulingBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class TaskSchedule
{
    /**
     * @var string
     */
    private $cronCommand;

    /**
     * @var bool
     */
    private $isWithoutOverlapping = false;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $options['cronCommand'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }
            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getCronCommand()
    {
        return $this->cronCommand;
    }

    /**
     * @return bool
     */
    public function getIsWithoutOverlapping()
    {
        return $this->isWithoutOverlapping;
    }
}