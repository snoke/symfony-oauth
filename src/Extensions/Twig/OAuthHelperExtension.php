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
            new TwigFunction('oauthApiKey', [$this, 'getApiKey']),
            new TwigFunction('oauthStyle', [$this, 'getStyle']),
            new TwigFunction('oauthSuccess', [$this, 'getSuccessRoute']),
        ];
    }

    public function getSuccessRoute($modul = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return $params[$modul]['success'];
    }
    public function getApiKey($modul = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return $params[$modul]['apiKey'];
    }
    public function getStyle($modul = "google"): string
    {
        $params = $this->params->get('snoke_o_auth');
        return json_encode($params[$modul]['style']);
    }
}