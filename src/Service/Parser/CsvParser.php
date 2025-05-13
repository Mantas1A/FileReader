<?php

namespace Service\Parser;

class CsvParser implements FileParserInterface
{
    private array $parsingErrors = [];

    public function parse(string $content): array
    {
        $rows = [];
        $rowNumber = 0;
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $content);
        rewind($handle);
        
        // Try to detect delimiter
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = $this->detectDelimiter($firstLine);
        
        // Read CSV with headers
        $headers = fgetcsv($handle, 0, $delimiter);
        if ($headers === false) {
            throw new \Exception('Empty CSV file');
        }
        $headerCount = count($headers);
        $rowNumber++;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;
            
            // Skip empty rows
            if (count($data) === 1 && empty($data[0])) {
                continue;
            }

            // Check for malformed rows
            if (count($data) !== $headerCount) {
                $this->parsingErrors[] = [
                    'type' => 'csv',
                    'line' => $rowNumber,
                    'message' => sprintf(
                        'Row %d has %d columns instead of expected %d',
                        $rowNumber,
                        count($data),
                        $headerCount
                    )
                ];
                continue;
            }

            // Check for empty or invalid values
            $hasInvalidValues = false;
            foreach ($data as $index => $value) {
                if (trim($value) === '') {
                    $this->parsingErrors[] = [
                        'type' => 'csv',
                        'line' => $rowNumber,
                        'message' => sprintf(
                            'Row %d has empty value in column "%s"',
                            $rowNumber,
                            $headers[$index]
                        )
                    ];
                    $hasInvalidValues = true;
                    break;
                }
            }

            if (!$hasInvalidValues) {
                $rows[] = array_combine($headers, $data);
            }
        }
        fclose($handle);
        
        if (empty($rows)) {
            throw new \Exception('No valid data rows found in CSV');
        }
        
        return $rows;
    }

    public function supports(string $mimeType, string $extension): bool
    {
        return in_array($mimeType, ['text/csv', 'application/csv']) || $extension === 'csv';
    }

    public function getParsingErrors(): array
    {
        return $this->parsingErrors;
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', '\t', '|'];
        $counts = [];
        
        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = count(str_getcsv($line, $delimiter));
        }
        
        return array_search(max($counts), $counts) ?: ',';
    }
}
