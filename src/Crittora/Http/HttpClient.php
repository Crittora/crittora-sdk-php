<?php

namespace Crittora\Http;

use Crittora\Exception\CrittoraException;

class HttpClient
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Send a POST request to the specified URL
     *
     * @param string $url The target URL
     * @param array $headers Request headers
     * @param mixed $payload Request body
     * @return array Decoded response
     * @throws CrittoraException
     */
    public function post(string $url, array $headers, $payload): array
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => $this->formatHeaders($headers),
                    'content' => json_encode($payload),
                    'ignore_errors' => true,
                ]
            ]);

            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                throw new CrittoraException('Request failed', 'REQUEST_ERROR');
            }

            $statusLine = $http_response_header[0];
            preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
            $statusCode = $match[1];

            if ($statusCode >= 400) {
                throw new CrittoraException(
                    "HTTP error! status: {$statusCode}, body: {$response}",
                    'HTTP_ERROR',
                    (int) $statusCode
                );
            }

            $responseData = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new CrittoraException('Invalid JSON response', 'INVALID_RESPONSE');
            }

            if (isset($responseData['body'])) {
                return json_decode($responseData['body'], true);
            }

            throw new CrittoraException('Invalid response format', 'INVALID_RESPONSE');
        } catch (CrittoraException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CrittoraException(
                "Request failed: {$e->getMessage()}",
                'REQUEST_ERROR'
            );
        }
    }

    /**
     * Format headers array into string format for stream context
     *
     * @param array $headers
     * @return string
     */
    private function formatHeaders(array $headers): string
    {
        return implode("\r\n", array_map(
            fn($key, $value) => "$key: $value",
            array_keys($headers),
            $headers
        ));
    }
} 