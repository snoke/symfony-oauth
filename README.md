# symfony-oauth
Symfony OAuth Bundle for fast integration of (yet only) Google OAuth Service
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
```composer req snoke/symfony-oauth```

## Configuration
edit ```config/packages/snoke_o_auth.yaml``` 
- set your **apiKey** (aka client-id)
- set a **success** route to redirect after successful login
($token placeholder will be replaced automatically by the google auth token)

```yaml
snoke_o_auth:
  google:
    apiKey: '<your google api-key/client-id here>'
    success: '/success/$token'
    style:
      theme: 'outline'        # outline / fill_blue / filled_black
      width: '120px'          # width in pixel
      locale: 'en_EN'	      # locale
      type: 'standard' 	      # standard / icon
      size: 'medium' 	      # small / medium / large
      text: 'signin'	      # signin / continue_with / signup_with / signin_with
      shape: 'rectangular'    # rectangular / pill / circle / square
      logo_alignment: 'left'  # left / center
```

## Usage
### frontend
add the following line to your twig template where you want the google button to appear 
```twig
{% include '@snoke_oauth/google/button.html.twig' %}
```
### backend
decode the token using the GoogleService provided in this bundle
```php
use Snoke\OAuth\Services\GoogleService;

class AuthController extends AbstractController
{
    #[Route('/success/{token}', name: 'auth_success')]
    public function success(GoogleService $googleService, string $token): Response
    {
        $claim = $googleService->decodeToken($token);
        $userEmail = $claim['email'];
        // ...
```