# PHP Skynet

A php package to use the Skynet Courier API.

## Installation

Require the package using composer:
```bash
composer require claassenmarius/php-skynet
```

## Usage

```php
use Claassenmarius\PhpSkynet\Skynet;

$skynet = new Skynet(
  'iDeliverTest',
  '!D3liver1!',
  '2',
  'J99133'
);

$response = $skynet->securityToken();
$token = $response->json();
```

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENCE.md)
