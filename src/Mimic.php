<?php

namespace PrairieDog;

use PDO;

class Mimic
{
    private array $replacements;

    public function __construct(
        private string $table,
        private string $path_key,
        private string $input,
        private string $output,
        private bool $force = false
    )
    {
        $this->log('Starting');

        $this->input = realpath($this->input);

        if (!file_exists($this->output))
        {
            mkdir($this->output, 0777, true);
        }

        $this->output = realpath($this->output);

        $this->log('path_key', $this->path_key);
        $this->log('table', $this->table);
        $this->log('in', $this->input);
        $this->log('out', $this->output);
    }

    public function __invoke(array $additional_replacements = [])
    {
        $this->log('Scanning Directories');

        $pascal_case = Str::pascalCase(Str::singular($this->table));

        $replacements = [
            $pascal_case => 'Table',
            '$' . $this->table => '$tables',
            '$' . Str::singular($this->table) => '$table',
            $this->table => 'tables',
            str_replace('_', '-', $this->table) => 'tables',
            Str::singular($this->table) => 'table',
        ];

        $this->replacements = array_merge($replacements, $additional_replacements);

        $this->scanDirectories($this->input);
    }

    private function log(string $prefix, string $suffix = null)
    {
        if ($suffix == null)
        {
            echo $prefix . '...' . PHP_EOL;
        }
        else
        {
            echo '  ' . str_pad($prefix, 16, ' ') . ': ' . $suffix . PHP_EOL;
        }
    }

    private  function scanDirectories($dir)
    {
        $files = scandir($dir);

        foreach ($files as  $value)
        {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (is_dir($path))
            {
                if ($value != "." && $value != "..")
                {
                    $this->scanDirectories($path);
                }
            }
            else
            {
                if (substr($path, -4) == '.php')
                {
                    if (str_contains($path, $this->path_key) !== false)
                    {
                        $this->buildOutput($path);
                    }
                }
            }
        }
    }

    private function buildOutput(string $path)
    {
        $output_file = str_replace($this->input, $this->output, $path);

        $key = Str::singular(Str::pascalCase($this->table));

        $output_file = str_replace($key, 'Table', $output_file);

        $output_file = str_replace('.php', '.stub',  $output_file);

        $output_dir = str_replace(basename($output_file), '', $output_file);

        if (!file_exists($output_dir))
        {
            mkdir($output_dir, 0777, true);
        }

        if (file_exists($output_file) && !$this->force)
        {
            $this->log(basename($output_file), 'skipping');

            return;
        }

        $contents = file_get_contents($path);

        $keys = array_keys($this->replacements);

        $values = array_values($this->replacements);

        $contents = str_replace($keys, $values, $contents);

        file_put_contents($output_file, $contents);

        $this->log(basename($output_file), 'generated' . ($this->force ? '*' : ''));
    }
}
