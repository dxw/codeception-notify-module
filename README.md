# codeception-notify-module

A Codeception module that extends [phiremock-codeception-extension](https://github.com/mcustiel/phiremock-codeception-extension) for testing applications that call GOV.UK Notify. 

The Phiremock extension is included in this module, so does not need to be installed separately.

## Installation

```bash
composer require --dev dxw/codeception-notify-module
```

## How to use 

Currently the module only tests requests to the Notify email endpoint.

### Setup

Configure the Phiremock extension in your codeception.yml to start the Phiremock server. You'll need to modify your Notify config so it hits this mock server when in the testing environment.

```yml
extensions:
    enabled:
        - \Codeception\Extension\Phiremock
    config:
        \Codeception\Extension\Phiremock:
            listen: 127.0.0.1:18080 # defaults to 0.0.0.0:8086
            bin_path: ../vendor/bin # defaults to codeception_dir/../vendor/bin 
            logs_path: /var/log/my_app/tests/logs # defaults to codeception's tests output dir
            debug: true # defaults to false
            startDelay: 1 # default to 0
            expectations_path: /my/expectations/path
```

Then enable the Notify module in your suite's configuration file, and configure it so it knows how to talk to the mock server:

```yml
modules:
    enabled:
        - \dxw\Codeception\Module\Notify:
            host: 127.0.0.1
            port: 18080
            resetBeforeEachTest: true # recommend setting this to true for predictable results
```

### Use 

The standard methods defined by the [Phiremock module](https://github.com/mcustiel/phiremock-codeception-extension) are all available. On top of these, there are some Notify-specific ones:

#### expectEmailRequestWithSuccessResponse

Allows you to specify that a request to the email endpoint should receive a 200 response. (It's important to note that the test won't fail if this request is not made - this just tells the mock server what response to supply if it is).

```php
$I->expectEmailRequestWithSuccessResponse();
```

#### expectEmailRequestWithFailureResponse

Allows you to specify that a request to the email endpoint should receive a 401 response. You can use this if you're handling all failure responses in the same way.

```php
$I->expectEmailRequestWithFailureResponse();
```

You can also specify the error code and body that should be returned, e.g.

```php 
$I->expectEmailRequestWithFailureResponse(403, '{"errors":[{"error":"AuthError", "message":"Invalid token"}]}');
```

Note that the body must be in the format expected by the [AlphaGov\Notifications\Exception\ApiException class](https://github.com/alphagov/notifications-php-client/blob/master/src/Exception/ApiException.php) otherwise the Notify client will error.

#### getRecipientEmailAddresses

Returns a chronological array of all email addresses that emails have been sent to.

```php
$recipients = $I->getRecipientEmailAddresses();
```

#### seeLastEmailWasSentTo

Verifies if the last request was sent to the email address you provide.

```php
$I->seeLastEmailWasSentTo('address@domain.com');
```

#### seeNotifyReceivedEmailRequests

Verifies how any email requests have been sent to Notify.

```php
$I->seeNotifyReceivedEmailRequests(5)
```

## Development

Install the dependencies:

```bash
composer install
```

Run the tests: 

```bash 
vendor/bin/codecept run unit 
```

Run the linter:

```bash
vendor/bin/php-cs-fixer fix 
```



