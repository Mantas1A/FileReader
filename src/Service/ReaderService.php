<?php

namespace Service;

use Config\FileConfig;
use Exception;
use Helper\TableDataHelper;
use Service\Parser\FileParserFactory;

class ReaderService
{
    private FileValidatorService $validator;
    private FileParserFactory $parserFactory;
    private TableDataHelper $tableHelper;
    private FileConfig $config;

    public function __construct()
    {
        $config = FileConfig::getInstance();
        $this->validator = new FileValidatorService($config);
        $this->parserFactory = new FileParserFactory();
        $this->tableHelper = new TableDataHelper();
        $this->config = $config;
    }

    public function getConfig(): FileConfig
    {
        return $this->config;
    }

    /**
     * Process the uploaded file
     * 
     * @param array $file The uploaded file array from $_FILES
     * @return array ['success' => bool, 'data' => mixed, 'error' => string|null, 'parsingErrors' => array, 'tableData' => array|null]
     */
    public function processFile(array $file): array
    {
        // Validate file
        $validation = $this->validator->validateFile($file);
        if (!$validation['isValid']) {
            return [
                'success' => false,
                'data' => null,
                'error' => $validation['error'],
                'parsingErrors' => [],
                'tableData' => null
            ];
        }

        try {
            // Get file content and type information
            $content = file_get_contents($file['tmp_name']);
            $mimeType = mime_content_type($file['tmp_name']);
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            // Get appropriate parser and parse content
            $parser = $this->parserFactory->getParser($mimeType, $extension);
            $data = $parser->parse($content);
            
            // Process data for table display if possible
            $tableData = $this->tableHelper->processDataForTable($data);

            return [
                'success' => true,
                'data' => $data,
                'error' => null,
                'parsingErrors' => method_exists($parser, 'getParsingErrors') ? $parser->getParsingErrors() : [],
                'tableData' => $tableData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
                'parsingErrors' => method_exists($parser ?? null, 'getParsingErrors') ? $parser->getParsingErrors() : [],
                'tableData' => null
            ];
        }
    }
}