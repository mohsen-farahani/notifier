<?php

namespace Asanbar\Notifier\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait RestConnector
{
    /**
     * Implements HTTP GET request via Guzzle
     *
     * @param string $uri
     * @param array|null $queries
     * @param array|null $request
     * @return array|mixed
     */
    public function get(string $uri, ?array $queries, ?array $request = [])
    {
        try {
            $params = [
                "query" => http_build_query($queries),
            ];
            if (!empty($request)) {
                $params = array_merge_recursive($params, $request);
            }

            return (new Client())->request(
                "GET",
                $uri,
                $params
            );
        } catch (RequestException $exception) {
            Log::error("Notifier: RestConnector GET Exception: " . $exception->getResponse()->getBody()->getContents());

            return response()->json(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    /**
     * Implements HTTP POST request via Guzzle
     *
     * @param $uri
     * @param $request
     * @return array|mixed
     */
    public function post($uri, $request)
    {
        try {
            return (new Client())->request(
                "POST",
                $uri,
                $request
            );
        } catch (RequestException $exception) {
            Log::error("Notifier: RestConnector POST Exception: " . $exception->getResponse()->getBody()->getContents());

            return response()->json(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }
}
