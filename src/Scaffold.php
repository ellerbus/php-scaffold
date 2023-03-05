<?php

namespace PrairieDog;

use PDO;

class Scaffold
{
    private array $replacements;
    private Table $table;

    public function __construct(
        string $dsn,
        string $user,
        string $pwd,
        string $table,
        private string $output,
        private string $input,
        private bool $force = false
    )
    {
        $this->log('Starting');

        $this->table = new Table($dsn, $user, $pwd, $table);

        $this->log('table', $this->table->name);
        $this->log('object', $this->table->pascalCase());
        $this->log('variable', $this->table->variableCase());
        $this->log('in', $this->input);
        $this->log('out', $this->output);
    }

    public function __invoke(array $additional_replacements = [])
    {
        $this->log('Scanning Directories');

        $replacements = [
            'Table' => $this->table->pascalCase(),
            '$table' => '$' . $this->table->variableCase(),
            'table' => $this->table->name
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

            $display = str_replace($this->input, '', $path);

            if (is_dir($path))
            {
                if ($value != "." && $value != "..")
                {
                    $this->scanDirectories($path);
                }
            }
            else
            {
                if (substr($path, -5) == '.stub')
                {
                    $this->buildOutput($path);
                }
            }
        }
    }

    private function buildOutput(string $path)
    {
        $output_file = str_replace($this->input, $this->output, $path);

        $output_file = str_replace('Table', $this->table->pascalCase(), $output_file);

        $output_file = str_replace('.stub', '.php', $output_file);

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

        ob_start();

        require_once $path;

        $contents = ob_get_clean();

        $keys = array_keys($this->replacements);

        $values = array_values($this->replacements);

        $contents = str_replace($keys, $values, $contents);

        file_put_contents($output_file, $contents);

        $this->log(basename($output_file), 'generated' . ($this->force ? '*' : ''));
    }
}
