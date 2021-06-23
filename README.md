# PHP Skynet

A php package to use the Skynet Courier API.

## Installation

Require the package using composer:
```bash
composer require claassenmarius/php-skynet
```

## Usage

Create an instance of [```Claassenmarius\PhpSkynet\SkynetSkynet```](./src/Skynet.php), passing in your skynet username,
password, account number and system id.


```php
use Claassenmarius\PhpSkynet\Skynet;

$skynet = new Skynet(
  'skynet_username',
  'skynet_password',
  'skynet_system_id',
  'skynet_account_number'
);
```
The following methods are available to [get a security token](#get-a-security-token), [validate a suburb/postcode combination](#validate-a-suburb-and-postal-code-combination), [get a list of postal
codes for a suburb](#get-a-list-of-postal-codes-for-a-suburb), [get a quote for a parcel](#get-a-quote-for-a-parcel), [get an ETA between two locations](#get-eta-between-two-locations), [generate a waybill](#generate-a-waybill), [obtain a POD image](#get-a-waybill-pod-image) and
[track a waybill](#track-a-waybill). Each method returns a new ```Claassenmarius\PhpSkynet\Response``` which 
[exposes methods](#response) to inspect the response.

#### Get a security token

```php
$response = $skynet->securityToken();
```

#### Validate a suburb and postal code combination
```php
$response = $skynet->validateSuburbAndPostalCode([
    'suburb' => 'Brackenfell',
    'postal-code' => '7560'
]);
```

#### Get a list of postal codes for a suburb
```php
$response = $skynet->postalCodesFromSuburb('Brackenfell');
```

#### Get a quote for a parcel
```php
$response = $skynet->quote([
    'collect-city' => 'Brackenfell',
    'deliver-city' => 'Stellenbosch',
    'deliver-postcode' => '7600',
    'service-type' => 'ON1',
    'insurance-type' => '1',
    'parcel-insurance' => '0',    
    'parcel-length' => 10, //cm
    'parcel-width' => 20, // cm
    'parcel-height' => 30, //cm
    'parcel-weight' => 20 //kg
]);
```

#### Get ETA between two locations
```php
$response = $skynet->deliveryETA([
    'from-suburb' => 'Brackenfell',
    'from-postcode' => '7560',
    'to-suburb' => 'Stellenbosch',
    'to-postcode' => '7600',
    'service-type' => 'ON1'
]);
```

#### Generate a waybill
```php
$response = $skynet->createWaybill([
    "customer-reference" => "Customer Reference",
    "GenerateWaybillNumber" => true,
    "service-type" => "ON1",
    "collection-date" => "2021-06-26",
    "from-address-1" => "3 Janie Street, Ferndale, Brackenfell",
    "from-suburb" => "Brackenfell",
    "from-postcode" => "7560",
    "to-address-1" => "15 Verreweide Street, Universiteitsoord, Stellenbosch",
    "to-suburb" => "Stellenbosch",
    "to-postcode" => "7600",
    "insurance-type" => "1",
    "insurance-amount" => "0",
    "security" => "N",
    "parcel-number" => "1",
    "parcel-length" => 10,
    "parcel-width" => 20,
    "parcel-height" => 30,
    "parcel-weight" => 10,
    "parcel-reference" => "12345",
    "offsite-collection" => true
]);
```

#### Get a waybill POD Image
```php
$response = $skynet->waybillPOD('your-waybill-number');
```

#### Track a waybill
```php
$response = $skynet->trackWaybill('your-waybill-number');
```

### Response
```Claassenmarius\PhpSkynet\Response``` provides the following methods to inspect the response.

#### Get the body of the response in string format:
```php
$securityToken = $response->body(); 
// "{"SecurityToken":"2_f77e4922-1407-485e-a0fa-4fdd5c29e9ca"}" 
```

#### Get the JSON decoded body of the response as an array or scalar value
```php
$securityToken = $response->json(); 
// ["SecurityToken" => "2_c767aa41-bca8-4084-82a0-69d8e27fba2c"] 
```

#### Get the JSON decoded body of the response as an object.
```php
$securityToken = $response->object(); 
// { +"SecurityToken": "2_c767aa41-bca8-4084-82a0-69d8e27fba2c" }
```
#### Get a header from the response.
```php
$header = $response->header('Content-Type'); 
// "application/json; charset=utf-8"
```

#### Get the headers from the response.
```php
$headers = $response->headers(); 
// Return an array of all headers
```
#### Get the status code of the response.
```php
$headers = $response->status(); 
// 200
```

#### Determine if the request was successful (Whether status code ```>=200``` & ```<300```)
```php
$headers = $response->successful(); 
// true
```

#### Determine if the response code was "OK". (Status code === ```200```)
```php
$headers = $response->ok(); 
// true
```

#### Determine if server error occurred. (Whether status code ```>=500```)
```php
$headers = $response->serverError(); 
// false
```

#### Determine if client or server error occurred.
```php
$headers = $response->failed(); 
// false
```



## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](./LICENCE.md)
