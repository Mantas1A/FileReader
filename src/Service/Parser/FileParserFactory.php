<?php

namespace Service\Parser;

class FileParserFactory
{
    /** @var FileParserInterface[] */
    private array $parsers;

    public function __construct()
    {
        $this->parsers = [
            new XmlParser(),
            new JsonParser(),
            new CsvParser()
        ];
    }

    /**
     * Get appropriate parser for the file type
     * 
     * @param string $mimeType The MIME type of the file
     * @param string $extension The file extension
     * @return FileParserInterface The appropriate parser
     * @throws \Exception If no suitable parser is found
     */
    public function getParser(string $mimeType, string $extension): FileParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($mimeType, $extension)) {
                return $parser;
            }
        }

        throw new \Exception('No suitable parser found for the file type');
    }
} 