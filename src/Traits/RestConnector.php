<?php

namespace AsanBar\Notifier\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
     * @return \Illuminate\Http\JsonResponse
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

            return response()->json(
                json_decode($response->getBody()->getContents()),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (RequestException $exception) {
            // TODO: dispatch exceptions through a job here

            return response()->json(
                json_decode($exception->getResponse()->getBody()->getContents()),
                $exception->getCode()
            );
        }
    }

    public function post($uri, $headers, $request)
    {
        $this->guzzleClient = $this->makeClient($headers);

        try {
            $response = $this->guzzleClient->request(
                "POST",
                $uri,
                ["body" => json_encode($request)]
            );

            return response()->json(
                json_decode($response->getBody()->getContents()),
                $response->getStatusCode(),
                $response->getHeaders()
            );
        } catch (RequestException $exception) {
            // TODO: catch exception and log

            return response()->json(
                json_decode($exception->getResponse()->getBody()->getContents()),
                $exception->getCode()
            );
        }
    }
}