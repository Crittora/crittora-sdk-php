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

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                $error = error_get_last();
                throw new CrittoraException(
                    'HTTP request failed: ' . ($error['message'] ?? 'Unknown error'),
                    'REQUEST_ERROR'
                );
            }

            $statusLine = $http_response_header[0];
            preg_match('{HTTP/\S*\s(\d{3})}', $statusLine, $match);
            $statusCode = $match[1] ?? 0;

            if ($statusCode >= 400) {
                throw new CrittoraException(
                    "HTTP error! status: {$statusCode}, body: {$response}",
                    'HTTP_ERROR',
                    (int) $statusCode
                );
            }

            $responseData = json_decode($response, true);

            if (isset($responseData['body'])) {
                return json_decode($responseData['body'], true);
            }

            throw new CrittoraException('Invalid response format', 'INVALID_RESPONSE');
        } catch (CrittoraException $e) {
            $this->logError($url, $headers, $payload, $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->logError($url, $headers, $payload, $e->getMessage());
            throw new CrittoraException(
                "Request failed: {$e->getMessage()}",
                'REQUEST_ERROR'
            );
        }
    }

    private function formatHeaders(array $headers): string
    {
        return implode("\r\n", array_map(
            fn($key, $value) => "$key: $value",
            array_keys($headers),
            $headers
        ));
    }

    private function logError(string $url, array $headers, $payload, string $errorMessage): void
    {
        error_log("HTTP POST Request Error:");
        error_log("URL: {$url}");
        error_log("Headers: " . json_encode($headers));
        error_log("Payload: " . json_encode($payload));
        error_log("Error: {$errorMessage}");
    }
}