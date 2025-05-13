<?php

namespace Service\Parser;

interface FileParserInterface
{
    /**
     * Parse the content of a file
     * 
     * @param string $content The file content to parse
     * @return array The parsed data
     * @throws \Exception If parsing fails
     */
    public function parse(string $content): array;

    /**
     * Check if this parser supports the given file type
     * 
     * @param string $mimeType The MIME type of the file
     * @param string $extension The file extension
     * @return bool True if this parser can handle the file type
     */
    public function supports(string $mimeType, string $extension): bool;
} 