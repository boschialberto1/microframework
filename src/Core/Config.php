<?php

namespace App\Core;

use Dotenv\Dotenv;
use Exception;

class Config
{
    private static ?Config $instance = null;
    protected array $settings = [];

    private function __construct()
    {
        $this->loadConfigFiles();
        $this->loadEnvFiles();
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /**
     * @throws Exception
     */
    private function loadConfigFiles(): void
    {
        foreach (glob(CONFIG_PATH . '/*.php') as $file) {
            $configSettings = require $file;
            if (is_array($configSettings)) {
                $this->settings = array_merge($this->settings, $configSettings);
            }
        }
    }

    private function loadEnvFiles(): void
    {
        $envFile = match (APP_ENVIRONMENT) {
            'development' => '.env.development',
            'testing' => '.env.testing',
            default => '.env',
        };

        $dotenv = Dotenv::createImmutable(ROOT_PATH, $envFile);
        $dotenv->load();

        $this->settings = array_merge($this->settings, $_ENV);
    }

    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}