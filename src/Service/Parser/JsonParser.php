<?php

namespace Service\Parser;

class JsonParser implements FileParserInterface
{
    private array $parsingErrors = [];

    public function parse(string $content): array
    {
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            return $data;
        } catch (\JsonException $e) {
            $this->parsingErrors[] = [
                'type' => 'json',
                'message' => 'JSON parsing error: ' . $e->getMessage()
            ];
            throw new \Exception('Invalid JSON format: ' . $e->getMessage());
        }
    }

    public function supports(string $mimeType, string $extension): bool
    {
        return $mimeType === 'application/json' || $extension === 'json';
    }

    public function getParsingErrors(): array
    {
        return $this->parsingErrors;
    }
}
