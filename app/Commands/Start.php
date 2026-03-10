<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class Start extends BaseCommand
{
    protected $group = 'CiPanel';
    protected $name = 'start';
    protected $description = 'Start server on specified host and port from baseURL';
    protected $usage = 'command:name [arguments] [options]';
    protected $arguments = [];
    protected $options = [];

    public function run(array $params)
    {
        // Get baseURL from environment
        $base_url = getenv('app.baseURL');

        // Parse baseURL to get host and port
        $parsed_url = parse_url($base_url);
        $base_host = isset($parsed_url['host']) ? $parsed_url['host'] : 'localhost';
        $base_port = isset($parsed_url['port']) ? $parsed_url['port'] : 8001;

        // Override CLI arguments to set the host and port
        $_SERVER['argv'][2] = '--host';
        $_SERVER['argv'][3] = $base_host;
        $_SERVER['argv'][4] = '--port';
        $_SERVER['argv'][5] = $base_port;
        $_SERVER['argc'] = 6;

        // Initialize CLI
        CLI::init();

        // Call CodeIgniter's serve command with the specified host and port
        $this->call('serve');
    }
}
