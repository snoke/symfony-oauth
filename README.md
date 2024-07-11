# symfony-oauth
Symfony OAuth2 Cliemt Bundle for fast integration of Github, Facebook and Google OAuth Service

(yet only supporting default scopes to get user login infos)
## Installation
add the custom repository to composer.json
```
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:snoke/symfony-oauth.git"
    }
],
```
install
```composer req snoke/symfony-oauth:dev-master```

## Configuration
edit ```config/packages/snoke_o_auth.yaml``` 
- set your **client-id** (aka api-key)
- set a redirect target **redirect_uri** after successful login (must be fqdn)
- set your **secret**

```yaml
snoke_o_auth:
  google:
    secret: '<your google client secret>'
    client_id: '<your google client-id>'
    redirect_uri: '<your redirect uri>' 
  github:
    client_id: '<your github client-id >'
    redirect_uri: '<your redirect uri>'
  facebook:
    secret: '<your google client secret>'
    client_id: '<your github client-id >'
    redirect_uri: '<your redirect uri>'
```

## Usage
### frontend
add the following line to your twig template where you want the sign in button to appear
#### github:
```twig
{% include '@snoke_oauth/github/button.html.twig' %}
```
#### google
```twig
{% include '@snoke_oauth/google/button.html.twig' %}
```

## styling
styling is possible with twig variables:
```twig
{% include '@snoke_oauth/google/button.html.twig' with {theme:'filled_black', text: 'signin_with' %}
{% include '@snoke_oauth/github/button.html.twig' with {theme:'filled_black', text: 'Sign in with Github' %}
{% include '@snoke_oauth/facebook/button.html.twig' with {theme:'filled_black', text: 'Sign in with Facebook' %}
```

![](./Docs/Images/buttons_black.PNG)

following styling options are provied:
```
      theme: 'outline'        # outline|filled_blue|filled_black
      width: '120px'          # width
      locale: 'en_EN'	      # locale (only for google)
      type: 'standard' 	      # standard|icon
      size: 'medium' 	      # small|medium|large
      text: 'signin'	      # github: <your button text> - google: signin|continue_with|signup_with|signin_with
      shape: 'rectangular'    # rectangular|pill|circle|square
      logo_alignment: 'left'  # left|center
```

### backend
decode the token using the Services provided in this bundle
```php
use Snoke\OAuth\Services\GithubService;
use Snoke\OAuth\Services\GoogleService;
use Snoke\OAuth\Services\FacebookService;

class AuthController extends AbstractController
{

    #[Route('/github_redirect_uri', name: 'auth_github_callback')]
    public function githubcallback(GithubService $githubService): Response
    {
        $claim = $githubService->getUser();
        $userEmail = $claim['email'];
        // ...
    }
    #[Route('/facebook_redirect_uri', name: 'auth_facebook_callback')]
    public function facebookcallback(FacebookService $facebookService): Response
    {
        $claim = $facebookService->getUser();
        $userEmail = $claim['email'];
        // ...
    }
    #[Route('/google_redirect_uri', name: 'auth_google_callback')]
    public function googlecallback(GoogleService $googleService, string $token): Response
    {
        $claim = $googleService->getUser();
        $userEmail = $claim['email'];
        // ...
```
