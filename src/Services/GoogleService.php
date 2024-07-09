<?php

namespace Snoke\OAuth\Services;

use Firebase\JWT\Key;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use phpseclib\Crypt\RSA;
use Firebase\JWT\JWT;
use phpseclib\Math\BigInteger;
use Snoke\OAuth\Exception\AuthServerNotAvailableException;

class GoogleService
{
    /**
     * @throws AuthServerNotAvailableException
     */
    public function decodeToken($idToken): array
    {
        $client = new Client();
        try {
            $response = $client->get('https://www.googleapis.com/oauth2/v3/certs');
        } catch (GuzzleException $e) {
            throw new AuthServerNotAvailableException('could not fetch certificates from https://www.googleapis.com/oauth2/v3/certs');
        }
        $certs = json_decode($response->getBody(), true);
        // Google certificates are in array, we need to get the correct one to verify the token
        $keys = [];
        foreach ($certs['keys'] as $key) {
            $publicKey = $this->createPemFromModulusAndExponent($key['n'], $key['e']);
            $kid = $key['kid'];
            $keys[$kid] = new Key($publicKey, $key['alg']);
        }

        // Decode the token header to find the key id (kid)
        $tokenParts = explode('.', $idToken);
        $header = json_decode(base64_decode($tokenParts[0]), true);

        return (array)JWT::decode($idToken, $keys[$header['kid']]);
    }

    private function createPemFromModulusAndExponent($n, $e): false|string|array
    {
        $modulus = $this->urlSafeB64Decode($n);
        $exponent = $this->urlSafeB64Decode($e);

        $modulus = new BigInteger($modulus, 256);
        $exponent = new BigInteger($exponent, 256);

        $rsa = new RSA();
        $rsa->loadKey(['n' => $modulus, 'e' => $exponent]);

        return $rsa->getPublicKey(RSA::PUBLIC_FORMAT_PKCS1);
    }

    private function urlSafeB64Decode($input): false|string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padLength = 4 - $remainder;
            $input .= str_repeat('=', $padLength);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

}
