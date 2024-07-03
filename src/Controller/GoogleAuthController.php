<?php

namespace Snoke\OAuth\Controller;

use Firebase\JWT\Key;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use phpseclib\Crypt\RSA;
use \Firebase\JWT\JWT;

class GoogleAuthController extends AbstractController
{
    #[Route('/auth/google', name: 'app_google_auth')]
    public function auth(): Response
    {
        return $this->render('google_auth/index.html.twig');
    }

    public function decodeIdToken($idToken) {
        $client = new Client();
        $response = $client->get('https://www.googleapis.com/oauth2/v3/certs');
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

        if (!isset($header['kid']) || !isset($keys[$header['kid']])) {
            throw new \Exception('Unable to find the correct key to verify the token');
        }

        return JWT::decode($idToken, $keys[$header['kid']]);
    }

    private function createPemFromModulusAndExponent($n, $e) {
        $modulus = $this->urlsafeB64Decode($n);
        $exponent = $this->urlsafeB64Decode($e);

        $modulus = new \phpseclib\Math\BigInteger($modulus, 256);
        $exponent = new \phpseclib\Math\BigInteger($exponent, 256);

        $rsa = new RSA();
        $rsa->loadKey(['n' => $modulus, 'e' => $exponent]);

        return $rsa->getPublicKey(RSA::PUBLIC_FORMAT_PKCS1);
    }

    private function urlsafeB64Decode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    #[Route('/auth/google/{token}', name: 'app_google_auth_token')]
    public function token(string $token): Response
    {
        $idToken = $token;  // Das empfangene ID-Token von Google
        echo '<pre>';
        var_dump($this->decodeIdToken($idToken));die;
    }

}
