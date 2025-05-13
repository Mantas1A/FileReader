<?php

namespace Config;

class FileConfig
{
    private static ?FileConfig $instance = null;
    private array $config;

    private function __construct()
    {
        $this->loadConfig();
    }

    public static function getInstance(): FileConfig
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        // Load from .env file if it exists
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with($line, '#')) continue;
                
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    putenv(trim($parts[0]) . '=' . trim($parts[1]));
                }
            }
        }

        // Check if ALLOWED_FILE_EXTENSION_TYPES is set
        $allowedExtensions = getenv('ALLOWED_FILE_EXTENSION_TYPES');
        if (!$allowedExtensions) {
            throw new \RuntimeException('ALLOWED_FILE_EXTENSION_TYPES environment variable must be set');
        }

        // Set configuration values
        $this->config = [
            'allowedMimeTypes' => array_map(
                'trim',
                explode(',', getenv('ALLOWED_MIME_TYPES') ?: 'text/csv,text/xml,application/xml,application/json')
            ),
            'allowedExtensionTypes' => array_map(
                'trim',
                explode(',', $allowedExtensions)
            )
        ];
    }

    public function getAllowedMimeTypes(): array
    {
        return $this->config['allowedMimeTypes'];
    }

    public function getAllowedExtensionTypes(): array
    {
        return $this->config['allowedExtensionTypes'];
    }
}
