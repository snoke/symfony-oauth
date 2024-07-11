<?php
namespace Snoke\OAuth\Extensions\Twig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OAuthHelperExtension extends AbstractExtension
{
    private ParameterBagInterface $params;

    private function getHost(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $serverName = $_SERVER['SERVER_NAME'];

        $serverAddress = $protocol . $serverName;

        if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
            $serverAddress .= ':' . $_SERVER['SERVER_PORT'];
        }
        return $serverAddress;
    }
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('oauthClientID', [$this, 'getClientID']),
            new TwigFunction('oauthStyle', [$this, 'getStyle']),
            new TwigFunction('oauthRedirectUri', [$this, 'getRedirectUri']),
            new TwigFunction('oauthAuthorizeUrl', [$this, 'getAuthorizeUrl']),
        ];
    }
    private function buildQueryString($params) {
        $queryString = '';
        foreach ($params as $key => $value) {
            if (!empty($queryString)) {
                $queryString .= '&';
            }
            $queryString .= $key . '=' . $value;
        }
        return $queryString;
    }
    public function getAuthorizeUrl($module = 'github',$clientID = null,$redirectUri = null,$scope = 'user'): string {

        $params = $this->params->get('snoke_o_auth');
        $clientID = $clientID ?? $params[$module]['client_id'];
        $redirectUri = $redirectUri ?? $params[$module]['redirect_uri'];
        return 'https://github.com/login/oauth/authorize?' . $this->buildQueryString([
                'client_id' => $clientID,
                'scope' => $scope,
                'state' => bin2hex(random_bytes(8)),
                'redirect_uri' => $redirectUri,
            ]);

    }

    public function getRedirectUri($module = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        $redirectUri = $params[$module]['redirect_uri'];
        return $redirectUri;
    }
    public function getClientID($module = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return $params[$module]['client_id'];
    }
}