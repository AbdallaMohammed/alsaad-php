<?php

namespace Alsaad\Entity;

/**
 * Identifies the Entity as using JsonSerializable to prepare request data.
 */
interface JsonUnserializableInterface
{
    /**
     * Update the object state with the json data (as an array)
     * @param $json
     * @return null
     */
    public function jsonUnserialize(array $json);
}
