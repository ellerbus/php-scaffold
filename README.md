# php-scaffold

PHP Scaffold/Code-Generator

A very simple database first code generator using a MYSQL database via PDO.

```php
$dsn = 'mysql:dbname=stratecise_db;host=127.0.0.1';
$user = 'root';
$password = 'sapass';

//  the table name to use as source references
$table = 'Table';

//  output for template results
$output = 'path/lib';

//  input path to .stub templates
$input = 'path/templates';

//  overwrite existing files without warning
$force = false;

$scaffold = new Scaffold($dsn, $user, $password, $table, $input, $output, $force);

$scaffold();
```
