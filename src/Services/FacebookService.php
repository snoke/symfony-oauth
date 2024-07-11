<?php

namespace Snoke\OAuth\Services;

use Snoke\OAuth\Exception\AuthServerException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Throwable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FacebookService
{
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getUser($accessToken = null) {
        $accessToken = $accessToken ?? $_GET['access_token'];
        $client = new Client();
        $params = $this->parameterBag->get('snoke_o_auth');

        $clientID = $clientID ?? $params["facebook"]['client_id'];
        $clientSecret = $clientSecret ?? $params["facebook"]['secret'];
        $response = $client->request('GET', 'https://graph.facebook.com/me', [
            'query' => ['access_token' => $accessToken, 'fields' => 'name,email']
        ]);

        $statusCode = $response->getStatusCode();
        return (array)json_decode($response->getBody()->getContents());
    }
}