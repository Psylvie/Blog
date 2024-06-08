<?php

namespace App\Models;

abstract class EntityModel
{
    public function __construct(?array $data = [])
    {
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
