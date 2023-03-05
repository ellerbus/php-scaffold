<?php

namespace PrairieDog;

final class Str
{
    /**
     *
     */
    public static function studlyCaps(string $text): string
    {
        $parts = explode('_', strtolower($text));

        return implode('', array_map('ucfirst', $parts));
    }

    /**
     *
     */
    public static function pascalCase(string $text): string
    {
        $parts = explode('_', strtolower($text));

        return  implode('', array_map('ucfirst', $parts));
    }

    /**
     *
     */
    public static function camelCase(string $text): string
    {
        $parts = explode('_', strtolower($text));

        $first  = isset($parts) ? $parts[0] : '';

        unset($parts[0]);

        return $first . implode('', array_map('ucfirst', $parts));
    }

    /**
     *
     */
    public static function singular(string $text): string
    {
        if (preg_match('/ies$/', $text))
        {
            $text = preg_replace('/ies$/', 'y', $text);
        }
        elseif (preg_match('/[xs]es$/', $text))
        {
            $text = preg_replace('/es$/', '', $text);
        }
        elseif (preg_match('/es$/', $text))
        {
            $text = preg_replace('/s$/', '', $text);
        }
        elseif (preg_match('/s$/', $text))
        {
            $text = preg_replace('/s$/', '', $text);
        }

        return $text;
    }
}
