<?php
namespace Snoke\OAuth\Extensions\Twig;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OAuthHelperExtension extends AbstractExtension
{
    private array $params;

    public function __construct(ParameterBagInterface $parameterBag) {
        $this->params = $parameterBag->get('snoke_o_auth');
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('oauthState', [$this, 'getState']),
            new TwigFunction('oauthClientID', [$this, 'getClientID']),
            new TwigFunction('oauthStyle', [$this, 'getStyle']),
            new TwigFunction('oauthRedirectUri', [$this, 'getRedirectUri']),
        ];
    }

    public function getState($module = "google"): string
    {
        return bin2hex(random_bytes(8));
    }
    public function getScope($module = "google"): string
    {
        return $this->params[$module]['scope'];
    }
    public function getRedirectUri($module = "google"): string
    {
        return $this->params[$module]['redirect_uri'];
    }
    public function getClientID($module = "google"): string
    {
        return $this->params[$module]['client_id'];
    }
}