<?php

namespace Comparon\SchedulingBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class TaskSchedule
{
    /** @var string */
    private $cronExpression;

    /** @var bool */
    private $isWithoutOverlapping = false;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Map first option to our variable name.
        if (isset($options['value'])) {
            $options['cronExpression'] = $options['value'];
            unset($options['value']);
        }

        // Map options to class properties.
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
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * @return bool
     */
    public function getIsWithoutOverlapping()
    {
        return $this->isWithoutOverlapping;
    }
}
