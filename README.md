# Client Library For PHP

*This library requires a minimum PHP version of 7.1*

This is the PHP client library for use Alsaad2's API. To use this, you'll need a Alsaad2 account.

* [Installation](#installation)
* [Usage](#usage)
* [Examples](#examples)

## Installation

To install the PHP client library to your project, we recommend using [Composer](https://getcomposer.org/).

```
require_once "vendor/autoload.php";
```

## Usage

If you're using Composer, make sure the autoloader is included in your project's bootstrap file:

```php
require_once "vendor/autoload.php";
```

Create a client with your Username and Password:

```php
$client = new Alsaad\Client([
    'username' => ALSAAD2_USERNAME,
    'password' => ALSAAD2_PASSWORD,
]);
```

## Examples

#### Sending a Message

To use Alsaad2's SMS API to send an SMS message, call the `$client->message()->send()` method.

```php
$message = $client->message()->send([
    'to' => ALSAAD2_TO, //can be array of numbers or string
    'from' => ALSAAD2_SENDER,
    'message' => 'Hello World'
]);
```
The API response data can be accessed as array properties of the message.

#### Accessing Response Data
When things go wrong, you'll receive an Exception. The exception class `Alsaad\Client\Exception\Request` support an additional `getEntity()` method which you can use in addition to `getCode()` and `getMessage()` to find out more about what went wrong. The entity returned will typically be an object related to the operation, or the response object from the API call.
