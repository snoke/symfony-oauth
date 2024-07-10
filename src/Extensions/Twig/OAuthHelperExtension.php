<?php
namespace Snoke\OAuth\Extensions\Twig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OAuthHelperExtension extends AbstractExtension
{
    private ParameterBagInterface $params;

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
    public function getAuthorizeUrl($module = 'github',$clientID = null,$redirectUri = null,$scope = 'user'): string {

        $params = $this->params->get('snoke_o_auth');
        $clientID = $clientID ?? $params[$module]['client_id'];
        $redirectUri = $redirectUri ?? $params[$module]['redirect_uri'];

        return 'https://github.com/login/oauth/authorize?' . http_build_query([
                'client_id' => $clientID,
                'redirect_uri' => $redirectUri,
                'scope' => $scope,
                'state' => bin2hex(random_bytes(8))
            ]);

    }

    public function getRedirectUri($module = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return $params[$module]['redirect_uri'];
    }
    public function getClientID($module = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return $params[$module]['client_id'];
    }
}