<?php

function get_TS_type($type)
{
    switch ($type) {
        case 'int':
            return 'number';
        case 'bool':
            return 'boolean';
        case 'array':
            return 'any[]';
        case 'object':
            return 'any';
        case 'string':
            return 'string';
        case 'mixed':
            return 'any';
        default:
            return 'any';
    }
}

function get_PHP_type($type)
{
    switch ($type) {
        case 'int':
            return 'int';
        case 'bool':
            return 'bool';
        case 'array':
            return 'array';
        case 'object':
            return 'array';
        case 'string':
            return 'string';
        case 'mixed':
            return 'mixed';
        default:
            return 'mixed';
    }
}

function array_to_file_contents($array)
{
    return implode("\n", $array) . "\n";
}

function generate_php_class_file($def_filename_without_extension, $class_member_variables)
{
    $class_name = str_replace('-', '', ucwords($def_filename_without_extension, '-'));

    $php_class_lines = [];
    $php_class_lines[] = '<?php';
    $php_class_lines[] = '';
    $php_class_lines[] = 'namespace App\\Models;';
    $php_class_lines[] = '';
    $php_class_lines[] = 'class ' . $class_name . ' {';

    foreach ($class_member_variables as $variable) {
        $name = $variable['name'];
        $type = $variable['type'];

        $php_type = get_PHP_type($type);
        $php_class_lines[] = '    public ' . $php_type . ' $' . $name . ';';
    }

    $php_class_lines[] = '}';

    $php_filename = __DIR__ . '/models/php/' . $class_name . '.php';
    file_put_contents($php_filename, array_to_file_contents($php_class_lines));
    echo 'generated ' . $php_filename . PHP_EOL;
}

function generate_ts_class_file($def_filename_without_extension, $class_member_variables)
{
    $class_name = str_replace('-', '', ucwords($def_filename_without_extension, '-'));

    $ts_class_lines = [];
    $ts_class_lines[] = 'export class ' . $class_name . ' {';

    $ts_constructor_body_lines = [];

    $ts_constructor_arguments = '';


    foreach ($class_member_variables as $variable) {
        $name = $variable['name'];
        $type = $variable['type'];

        $ts_type = get_TS_type($type);
        $ts_class_lines[] = '    ' . $name . ': ' . $ts_type;

        $ts_constructor_arguments .= $name . ': ' . $ts_type . ', ';
        $ts_constructor_body_lines[] = '        this.' . $name . ' = ' . $name;
    }

    if (strlen($ts_constructor_arguments) > 2) {
        $ts_constructor_arguments = substr($ts_constructor_arguments, 0, -2);
    }
    $ts_constructor_lines = [];
    $ts_constructor_lines[] = '    constructor(' . $ts_constructor_arguments . ') {';
    array_push($ts_constructor_lines, ...$ts_constructor_body_lines);
    $ts_constructor_lines[] = '    }';

    $ts_class_lines[] = '';
    array_push($ts_class_lines, ...$ts_constructor_lines);
    $ts_class_lines[] = '}';

    $ts_filename = __DIR__ . '/models/ts/' . $def_filename_without_extension . '.model.ts';
    file_put_contents($ts_filename, array_to_file_contents($ts_class_lines));
    echo 'generated ' . $ts_filename . PHP_EOL;
}

function process_def_files()
{
    $filesystem_iterator = new FilesystemIterator(__DIR__ . '/def/');
    foreach ($filesystem_iterator as $fileinfo) {
        $filename = $fileinfo->getFilename();
        $filename_without_extension = substr($filename, 0, -4);
        $def_lines = file(__DIR__ . '/def/' . $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $class_member_variables = [];
        foreach ($def_lines as $line) {
            $line = trim($line);
            $words = explode(' ', $line);
            $class_member_variables[] = [
                'type' => $words[0],
                'name' => $words[1]
            ];
        }

        generate_php_class_file($filename_without_extension, $class_member_variables);
        generate_ts_class_file($filename_without_extension, $class_member_variables);
    }

    echo 'done' . PHP_EOL;
}

process_def_files();
