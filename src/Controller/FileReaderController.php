<?php

namespace Controller;

use Service\ReaderService;

class FileReaderController
{
    private ReaderService $readerService;

    public function __construct()
    {
        $this->readerService = new ReaderService();
    }

    /**
     * Handles both GET and POST requests for the file reader page
     */
    public function index(): void
    {
        $error = null;
        $data = null;
        $parsingErrors = [];
        $result = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_FILES['uploaded_file'])) {
                $error = 'No file was uploaded';
            } else {
                $result = $this->readerService->processFile($_FILES['uploaded_file']);
                
                if (!$result['success']) {
                    $error = $result['error'];
                } else {
                    $data = $result['data'];
                }
                
                // Always pass parsing errors, even if the overall process succeeded
                $parsingErrors = $result['parsingErrors'] ?? [];
            }
        }

        // Include the template and pass the variables
        $templatePath = __DIR__ . '/../../templates/fileReader.html';
        
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            http_response_code(500);
            echo 'Template file not found';
        }
    }
}
