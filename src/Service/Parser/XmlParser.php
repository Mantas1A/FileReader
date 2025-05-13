<?php

namespace Service\Parser;

use SimpleXMLElement;

class XmlParser implements FileParserInterface
{
    private array $parsingErrors = [];

    public function parse(string $content): array
    {
        libxml_use_internal_errors(true);
        try {
            $xml = new SimpleXMLElement($content);
            
            // Check if all data is within a single root element
            $rootChildren = $xml->children();
            if ($rootChildren->count() > 0) {
                // Get the name of the first child
                $firstName = $rootChildren[0]->getName();
                
                // Check if all children have the same name (indicating a collection)
                $allSameName = true;
                foreach ($rootChildren as $child) {
                    if ($child->getName() !== $firstName) {
                        $allSameName = false;
                        break;
                    }
                }
                
                // If all children have the same name, treat root as a container
                if ($allSameName) {
                    $result = [];
                    foreach ($rootChildren as $child) {
                        $result[] = $this->xmlElementToArray($child);
                    }
                } else {
                    // If children have different names, include the root element
                    $result = $this->xmlElementToArray($xml);
                }
            } else {
                // If no children, process the root element itself
                $result = $this->xmlElementToArray($xml);
            }
            
            // Check for any XML parsing errors
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                $this->parsingErrors[] = [
                    'type' => 'xml',
                    'line' => $error->line,
                    'message' => trim($error->message)
                ];
            }
            libxml_clear_errors();
            
            if (!empty($this->parsingErrors)) {
                throw new \Exception('XML parsing errors found');
            }
            
            return $result;
        } catch (\Exception $e) {
            if (empty($this->parsingErrors)) {
                $this->parsingErrors[] = [
                    'type' => 'xml',
                    'line' => 0,
                    'message' => $e->getMessage()
                ];
            }
            throw $e;
        }
    }

    public function supports(string $mimeType, string $extension): bool
    {
        return in_array($mimeType, ['text/xml', 'application/xml']) || $extension === 'xml';
    }

    public function getParsingErrors(): array
    {
        return $this->parsingErrors;
    }

    private function xmlElementToArray(SimpleXMLElement $element): array
    {
        $result = [];
        
        // Convert child elements to array
        foreach ($element->children() as $child) {
            $value = trim((string) $child);
            
            // If the child has its own children, convert them too
            if ($child->count()) {
                $value = $this->xmlElementToArray($child);
            }
            
            $result[$child->getName()] = $value;
        }
        
        // Add attributes if any
        foreach ($element->attributes() as $name => $value) {
            $result['@' . $name] = (string) $value;
        }
        
        return $result;
    }
} 