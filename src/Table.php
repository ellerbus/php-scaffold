<?php

namespace PrairieDog;

use PDO;

final class Table
{
    public $columns = [];

    private PDO $pdo;

    public function __construct(
        private string $dsn,
        private string $user,
        private string $password,
        public string $name
    )
    {
        $this->pdo = new PDO($dsn, $user, $password);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->loadColumns();
    }

    private function loadColumns()
    {
        $sql = "DESC {$this->name}";

        $stmt = $this->pdo->query($sql, PDO::FETCH_ASSOC);

        $rows = $stmt->fetchAll();

        foreach ($rows as $row)
        {
            $column = new Column(
                name: $row['Field'],
                type: $row['Type'],
                nullable: $row['Null'] == 'YES',
                key: $row['Key'],
                extra: $row['Extra']
            );

            $this->columns[$column->name] = $column;
        }
    }

    /**
     *
     */
    public function pascalCase(): string
    {
        return Str::pascalCase($this->name);
    }

    /**
     *
     */
    public function variableCase(): string
    {
        $parts = explode('_', strtolower($this->name));

        $last = $parts[count($parts) - 1];

        $last = Str::singular($last);

        return $last;
    }
}
