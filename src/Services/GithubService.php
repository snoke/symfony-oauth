<?php

namespace Snoke\OAuth\Services;

use GuzzleHttp\Client;
use Snoke\OAuth\Exception\AuthServerException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GithubService
{
    private const GITHUB_TOKEN_API = 'https://github.com/login/oauth/access_token';
    private const GITHUB_USER_API = 'https://api.github.com/user';

    private ParameterBagInterface $parameterBag;
    private Client $client;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->client = new Client();
    }
    private function getToken($clientID = null, $clientSecret = null, $redirectUri = null) {
        if (!$clientID || !$clientSecret || !$redirectUri) {
            $params = $this->parameterBag->get('snoke_o_auth');
            $clientID = $clientID ?? $params["github"]['client_id'];
            $redirectUri = $redirectUri ?? $params["github"]['redirect_uri'];
            $clientSecret = $clientSecret ?? $params["github"]['secret'];
        }
        $tokenUrl = self::GITHUB_TOKEN_API;
        $postData = [
            'client_id' => $clientID,
            'client_secret' => $clientSecret,
            'code' => $_GET['code'],
            'redirect_uri' => $redirectUri,
            'state' => $_GET['state']
        ];


        $response = $this->client->post($tokenUrl, [
            'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
            'form_params' => $postData,
        ]);

        $body = (string) $response->getBody();
        parse_str($body, $responseData);

        if (!isset($responseData['access_token'])) {
            throw new AuthServerException($responseData['error'] ?? 'unknown error retrieving access token');
        }

        return $responseData['access_token'];
    }

    public function getUser($accessToken = null) {
        $accessToken = $accessToken ?? $this->getToken();
        $userUrl = self::GITHUB_USER_API;
        $response = $this->client->get($userUrl, [
            'headers' => [
                'User-Agent' => 'PHP',
                'Authorization' => 'token ' . $accessToken
            ]
        ]);

        $user = json_decode($response->getBody(), true);

        if (!isset($user['login'])) {
            throw new AuthServerException($user['error'] ?? 'Unknown error retrieving user');
        }

        return $user;
    }
}