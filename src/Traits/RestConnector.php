<?php

namespace AsanBar\Notifier\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait RestConnector
{
    protected $guzzleClient = null;

    /**
     * Instantiates a Guzzle HTTP client if not available
     *
     * @param array $headers
     * @return Client|null
     */
    private function makeClient($headers = [])
    {
        if(!$this->guzzleClient) {
            $this->guzzleClient = new Client([
                "headers" => $headers,
                "default" => [
                    "verify" => false
                ]
            ]);
        }

        return $this->guzzleClient;
    }

    /**
     * Implements HTTP GET request via Guzzle
     *
     * @param string $uri
     * @param $headers
     * @param array $request
     * @return array|mixed
     */
    public function get($uri, $headers, $request = [])
    {
        $this->guzzleClient = $this->makeClient($headers);

        try {
            $response = $this->guzzleClient->request(
                "GET",
                $uri,
                ["query" => http_build_query($request)]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $exception) {
            Log::error("RestConnector GET Exception: " . $exception->getResponse()->getBody()->getContents());

            return [];
        }
    }

    /**
     * Implements HTTP POST request via Guzzle
     *
     * @param $uri
     * @param $headers
     * @param $request
     * @return array|mixed
     */
    public function post($uri, $headers, $request)
    {
        $this->guzzleClient = $this->makeClient($headers);

        try {
            $response = $this->guzzleClient->request(
                "POST",
                $uri,
                ["body" => json_encode($request)]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $exception) {
            Log::error("RestConnector POST Exception: " . $exception->getResponse()->getBody()->getContents());

            return [];
        }
    }
}