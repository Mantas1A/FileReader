<?php

namespace Helper;

class TableDataHelper
{
    /**
     * Process data for table display
     * 
     * @param mixed $data The parsed data
     * @return array|null ['headers' => array, 'rows' => array] or null if data is not suitable for table
     */
    public function processDataForTable(mixed $data): ?array
    {
        // Check if data is an array and has at least one element
        if (!is_array($data) || empty($data)) {
            return null;
        }

        // Check if first element is an array (indicates tabular data)
        if (!is_array($data[0])) {
            return null;
        }

        // Get all unique keys from the data
        $headers = $this->getAllKeys($data);

        // Process rows
        $rows = [];
        foreach ($data as $row) {
            $processedRow = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? null;
                $processedRow[$header] = is_array($value) ? json_encode($value) : $value;
            }
            $rows[] = $processedRow;
        }

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    /**
     * Get all unique keys from an array of arrays
     */
    private function getAllKeys(array $data): array
    {
        $keys = [];
        array_walk_recursive($data, function($value, $key) use (&$keys) {
            if (!in_array($key, $keys)) {
                $keys[] = $key;
            }
        });
        return $keys;
    }
} 