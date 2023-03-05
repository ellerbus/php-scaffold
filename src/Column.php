<?php

namespace PrairieDog;

final class Column
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $nullable,
        public string $key,
        public string $extra
    )
    {
    }

    public  function studlyCaps()
    {
        $parts = explode('_', strtolower($this->name));

        return implode('', array_map('ucfirst', $parts));
    }

    public  function camelCase()
    {
        $parts = explode('_', strtolower($this->name));

        $first  = isset($parts) ? $parts[0] : '';

        unset($parts[0]);

        return $first . implode('', array_map('ucfirst', $parts));
    }
}
