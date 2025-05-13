<?php

namespace Service;

use Config\FileConfig;

class FileValidatorService
{
    private FileConfig $config;

    public function __construct(FileConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Validate the uploaded file
     * 
     * @param array $file The uploaded file array from $_FILES
     * @return array ['isValid' => bool, 'error' => string|null]
     */
    public function validateFile(array $file): array
    {
        try {
            // Validate file upload
            if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                return [
                    'isValid' => false,
                    'error' => $this->getUploadErrorMessage($file['error'] ?? -1)
                ];
            }

            // Get file extension and mime type
            $mimeType = mime_content_type($file['tmp_name']);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Validate file type
            if (!$this->isValidFileType($mimeType, $extension)) {
                return [
                    'isValid' => false,
                    'error' => sprintf('Invalid file type. Only %s files are allowed.', 
                        strtoupper(implode(', ', $this->config->getAllowedExtensionTypes()))
                    )
                ];
            }

            return [
                'isValid' => true,
                'error' => null
            ];
        } catch (\RuntimeException $e) {
            return [
                'isValid' => false,
                'error' => 'Configuration error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validates file type based on mime type and extension
     */
    private function isValidFileType(string $mimeType, string $extension): bool
    {
        // Check mime type
        if (in_array($mimeType, $this->config->getAllowedMimeTypes())) {
            return true;
        }

        // Check extension
        return in_array($extension, $this->config->getAllowedExtensionTypes());
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage(int $error): string
    {
        return match($error) {
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
            default => 'Unknown upload error'
        };
    }
}
