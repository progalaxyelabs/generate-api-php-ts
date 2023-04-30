<?php
if($argc === 1) {
    echo 'service name required' . PHP_EOL . 'ex: php ' . basename(__FILE__) . ' auth' . PHP_EOL;
    die(0);
}

$current_dir = __DIR__;
$angular_project_dir = '/home/smk/projects/active/progalaxy/progalaxy3/';

$input_service_name = $argv[1];
$api_service_name = $input_service_name . '-api';
$type_name = str_replace('-', '', ucwords($input_service_name, '-'));
$api_type_name = $type_name . 'Api';
// $service_file_partial_name = $input_service_name . '-api';
$project_api_file_path = 'services/api/';

chdir($angular_project_dir);
$service_file_path = $angular_project_dir . 'src/app/' . $project_api_file_path . $api_service_name . '.service.ts';
$spec_file_path = str_replace('.service.ts', '.service.spec.ts', $service_file_path);

unlink($service_file_path);
unlink($spec_file_path);

exec('ng g s ' . $project_api_file_path  . $api_service_name);

$contents = file_get_contents($service_file_path);

$default_import_line = 'import { Injectable } from \'@angular/core\';';
$import_lines = [];
$import_lines[] = $default_import_line;
$import_lines[] = "import { IApiService } from 'src/app/models/i-api-service.model';";
$import_lines[] = "import { ApiService } from '../api.service';";
$import_lines[] = "import { TypeName } from 'src/app/models/InputServiceName.model';";
$import_lines[] = "import { TypeNameApiListRequest } from 'src/app/models/InputServiceName-api-list-request.model';";
$import_lines[] = "import { TypeNameApiAddRequest } from 'src/app/models/InputServiceName-api-add-request.model';";
$import_lines[] = "import { TypeNameApiUpdateRequest } from 'src/app/models/InputServiceName-api-update-request.model';";
$import_lines[] = "import { TypeNameApiRemoveRequest } from 'src/app/models/InputServiceName-api-remove-request.model';";
$import_lines[] = "import { TypeNameApiDetailRequest } from 'src/app/models/InputServiceName-api-detail-request.model';";

foreach($import_lines as &$line) {        
    $line = str_replace('TypeName', $type_name, $line); 
    $line = str_replace('InputServiceName', $input_service_name, $line);
}

$import_lines_str = implode("\n", $import_lines);
$contents = str_replace($default_import_line, $import_lines_str, $contents);

$class_implementation = <<<END

    constructor(private api: ApiService) {

    }

    async list(options: {$type_name}ApiListRequest | undefined): Promise<{$type_name}[]> {
        throw new Error('Method not implemented.');
    }

    async detail(options: {$type_name}ApiDetailRequest): {$type_name} {
        throw new Error('Method not implemented.');
    }

    async add(options: {$type_name}ApiAddRequest): Promise<{$type_name} | null> {
        throw new Error('Method not implemented.');
    }

    async remove(options: {$type_name}ApiRemoveRequest): Promise<boolean> {
        throw new Error('Method not implemented.');
    }

    async update(options: {$type_name}ApiUpdateRequest): Promise<{$type_name}> {
        throw new Error('Method not implemented.');
    }

END;

$class_name = $type_name . 'ApiService';
$class_declaration_line = 'export class ' . $class_name .' {';
$contents = str_replace($class_name, $class_name .' implements IApiService', $contents);
$contents = str_replace('constructor() { }', $class_implementation, $contents);


file_put_contents($service_file_path, $contents);
var_dump($contents);

chdir($current_dir);

