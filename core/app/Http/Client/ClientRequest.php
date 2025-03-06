<?php

namespace App\Http\Client;

use RuntimeException;
use WP_REST_Response;
use App\Support\Tools;
use InvalidArgumentException;
use App\Http\Response\WP_Response;
use App\Interfaces\MethodInterface;

/**
 * Class ClientRequest
 * Handles HTTP requests using cURL with configurable options for authentication, proxy, cookies, and more.
 */
class ClientRequest
{
    private static ?string $cookie = null;
    private static ?string $cookieFile = null;
    private static array $curlOpts = []; // Can be set externally; validate before use
    private static array $defaultHeaders = [];
    private static ?int $socketTimeout = 30;
    private static bool $verifyPeer = false;
    private static bool $verifyHost = false;
    private static string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36';

    private static array $auth = [
        'user' => '',
        'pass' => '',
        'method' => CURLAUTH_BASIC
    ];

    private static array $proxy = [
        'port' => false,
        'tunnel' => false,
        'address' => false,
        'type' => CURLPROXY_HTTP,
        'auth' => [
            'user' => '',
            'pass' => '',
            'method' => CURLAUTH_BASIC
        ]
    ];

    /**
     * Check if the HTTP method is allowed.
     */
    private static function httpMethodIsAllowed(string $method): bool
    {
        return in_array($method, [
            MethodInterface::GET,
            MethodInterface::POST,
            MethodInterface::PUT,
            MethodInterface::DELETE,
            MethodInterface::PATCH,
            MethodInterface::HEAD,
            MethodInterface::OPTIONS,
            MethodInterface::CONNECT,
            MethodInterface::TRACE
        ]);
    }

    /**
     * Merge cURL options while preserving numeric keys.
     */
    private static function mergeCurlOptions(array $defaultOptions, array $newOptions): array
    {
        $merged = $defaultOptions;

        foreach ($newOptions as $key => $value) {
            if (isset($merged[$key]) && is_array($merged[$key]) && is_array($value)) {
                $merged[$key] = array_merge_recursive($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        $validated = self::validateCurlOptionsArray($merged);
        return $validated;
    }

    /**
     * Validate that all keys in the cURL options array are valid cURL constants.
     * Throws InvalidArgumentException if invalid options are found.
     */
    private static function validateCurlOptionsArray(array $options): array
    {
        $validOptions = [];
        foreach ($options as $key => $value) {
            if (!is_int($key) || !in_array($key, [
                CURLOPT_URL, CURLOPT_RETURNTRANSFER, CURLOPT_HEADER, CURLOPT_TIMEOUT,
                CURLOPT_FOLLOWLOCATION, CURLOPT_USERAGENT, CURLOPT_HTTPHEADER,
                CURLOPT_SSL_VERIFYPEER, CURLOPT_SSL_VERIFYHOST, CURLOPT_CUSTOMREQUEST,
                CURLOPT_POSTFIELDS, CURLOPT_NOBODY, CURLOPT_USERPWD, CURLOPT_HTTPAUTH,
                CURLOPT_PROXY, CURLOPT_PROXYPORT, CURLOPT_PROXYTYPE, CURLOPT_HTTPPROXYTUNNEL,
                CURLOPT_PROXYUSERPWD, CURLOPT_PROXYAUTH, CURLOPT_COOKIE, CURLOPT_COOKIEJAR,
                CURLOPT_COOKIEFILE
            ])) {
                throw new InvalidArgumentException("Invalid cURL option key: $key. Must be a valid CURLOPT_* constant.");
            }
            $validOptions[$key] = $value;
        }
        return $validOptions;
    }

    /**
     * Send a GET request to a URL.
     */
    public static function get($url, $headers = [], $parameters = null, $username = null, $password = null): WP_REST_Response
    {
        return self::send(MethodInterface::GET, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send a HEAD request to a URL.
     */
    public static function head($url, $headers = [], $parameters = null, $username = null, $password = null): WP_REST_Response
    {
        return self::send(MethodInterface::HEAD, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send an OPTIONS request to a URL.
     */
    public static function options($url, $headers = [], $parameters = null, $username = null, $password = null): WP_REST_Response
    {
        return self::send(MethodInterface::OPTIONS, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send a CONNECT request to a URL.
     */
    public static function connect($url, $headers = [], $parameters = null, $username = null, $password = null): WP_REST_Response
    {
        return self::send(MethodInterface::CONNECT, $url, $parameters, $headers, $username, $password);
    }

    /**
     * Send a POST request to a URL.
     */
    public static function post($url, $headers = [], $body = null, $username = null, $password = null): WP_REST_Response
    {
        return self::send(MethodInterface::POST, $url, $body, $headers, $username, $password);
    }

    /**
     * Send an HTTP request and return WP_REST_Response.
     *
     * @throws InvalidArgumentException If the method is invalid or cURL options are invalid.
     * @throws RuntimeException If cURL execution fails.
     */
    private static function send(
        string $method,
        string $url,
        mixed $body = null,
        array $headers = [],
        ?string $username = null,
        ?string $password = null
    ): WP_REST_Response {
        if (!self::httpMethodIsAllowed($method)) {
            throw new InvalidArgumentException("Invalid HTTP method: $method");
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => self::$socketTimeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => self::$userAgent,
            CURLOPT_HTTPHEADER => array_merge(self::$defaultHeaders, $headers),
            CURLOPT_SSL_VERIFYPEER => config('app.env') == __ENV_IS_PRODUCTION__, // Dynamic based on env
            CURLOPT_SSL_VERIFYHOST => self::$verifyHost ? 2 : 0,
        ];

        self::configureAuthentication($options, $username, $password);
        self::configureProxy($options);
        self::configureCookies($options);
        self::configureRequestBodyAndMethod($options, $method, $body, $url);

        // Merge with any pre-set curl options and validate
        $options = self::mergeCurlOptions(self::$curlOpts, $options);

        // Safeguard: Verify array structure before passing to cURL
        if (array_keys($options) === range(0, count($options) - 1)) {
            throw new InvalidArgumentException('cURL options array has been re-indexed. Keys are missing.');
        }

        return self::executeRequest($options);
    }

    private static function configureAuthentication(array &$options, ?string $username, ?string $password): void
    {
        $authUser = $username ?? self::$auth['user'];
        $authPass = $password ?? self::$auth['pass'];

        if (!empty($authUser) && !empty($authPass)) {
            $options[CURLOPT_USERPWD] = "$authUser:$authPass";
            $options[CURLOPT_HTTPAUTH] = self::$auth['method'];
        }
    }

    private static function configureProxy(array &$options): void
    {
        if (self::$proxy['address']) {
            $options[CURLOPT_PROXY] = self::$proxy['address'];
            $options[CURLOPT_PROXYPORT] = self::$proxy['port'];
            $options[CURLOPT_PROXYTYPE] = self::$proxy['type'];
            $options[CURLOPT_HTTPPROXYTUNNEL] = self::$proxy['tunnel'];

            if (self::$proxy['auth']['user'] && self::$proxy['auth']['pass']) {
                $options[CURLOPT_PROXYUSERPWD] = self::$proxy['auth']['user'] . ':' . self::$proxy['auth']['pass'];
                $options[CURLOPT_PROXYAUTH] = self::$proxy['auth']['method'];
            }
        }
    }

    private static function configureCookies(array &$options): void
    {
        if (self::$cookie) {
            $options[CURLOPT_COOKIE] = self::$cookie;
        } elseif (self::$cookieFile) {
            $options[CURLOPT_COOKIEJAR] = self::$cookieFile;
            $options[CURLOPT_COOKIEFILE] = self::$cookieFile;
        }
    }

    private static function configureRequestBodyAndMethod(array &$options, string $method, mixed $body, string &$url): void
    {
        if (in_array($method, [MethodInterface::POST, MethodInterface::PUT, MethodInterface::DELETE, MethodInterface::PATCH])) {
            $options[CURLOPT_POSTFIELDS] = is_array($body) ? http_build_query($body) : ($body ?? '');
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        } elseif ($method === MethodInterface::GET && !empty($body)) {
            $url .= '?' . http_build_query($body);
            $options[CURLOPT_URL] = $url;
        } elseif ($method === MethodInterface::HEAD) {
            $options[CURLOPT_NOBODY] = true;
        } else {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
    }

    private static function executeRequest(array $options): WP_REST_Response
    {
        try {
            $handle = curl_init();
            curl_setopt_array($handle, $options);

            $response = curl_exec($handle);
            $statusCode = self::getInfo($handle, CURLINFO_HTTP_CODE);
            $errno = curl_errno($handle);
            $error = curl_error($handle);

            $headerSize = self::getInfo($handle, CURLINFO_HEADER_SIZE);
            $headersRaw = substr($response, 0, $headerSize);
            $bodyRaw = substr($response, $headerSize);
            $headersArray = array_filter(explode(PHP_EOL, trim($headersRaw)));
            $contentType = strtolower(trim(explode(';', self::getInfo($handle, CURLINFO_CONTENT_TYPE))[0] ?? ''));

            curl_close($handle);

            // Handle cURL errors
            if ($errno) {
                $errorMessage = match ($errno) {
                    CURLE_COULDNT_RESOLVE_HOST => 'Invalid URL: Could not resolve host.',
                    CURLE_COULDNT_CONNECT => 'Connection failed: Could not connect to server.',
                    default => "cURL Error: $error",
                };
                $responseData = [
                    'success' => false,
                    'status_code' => 0, // Use 0 for cURL errors as no HTTP response was received
                    'headers' => [],
                    'data' => null,
                    'message' => $errorMessage,
                ];
                return new WP_REST_Response($responseData, 0);
            }

            // Parse the body based on content type
            $parsedBody = match (true) {
                str_ends_with($contentType, '/json') => json_decode($bodyRaw),
                str_ends_with($contentType, '/xml') => simplexml_load_string($bodyRaw),
                default => $bodyRaw,
            };

            // Determine success based on HTTP status code
            $isSuccess = ($statusCode >= 200 && $statusCode < 300);
            $responseData = [
                'success' => $isSuccess,
                'status_code' => $statusCode,
                'headers' => $headersArray,
                'data' => $parsedBody,
                'message' => $isSuccess ? '' : ($parsedBody->requestError->serviceException->text ?? 'Request failed'),
            ];

            return new WP_REST_Response($responseData, $statusCode, $headersArray);

        } catch (\Exception $e) {
            return WP_Response::error($e->getMessage(), $e->getCode());
        }
    }

    public static function getInfo($curlHandle, $opt = false)
    {
        return $opt ? curl_getinfo($curlHandle, $opt) : curl_getinfo($curlHandle);
    }

    private static function contentTypeIs(string $contentType): string
    {
        $contentType = strtoupper($contentType);
        return match (true) {
            str_ends_with($contentType, '/JSON') => 'json',
            str_ends_with($contentType, '/XML') => 'xml',
            default => 'html'
        };
    }
}