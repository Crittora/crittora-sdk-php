# Crittora SDK for PHP

The Crittora SDK for PHP provides a simple interface for authentication, encryption, and decryption of data using the Crittora API. This document will guide you through the setup and usage of the SDK, as well as running the demo application.

## Table of Contents

- [Crittora SDK for PHP](#crittora-sdk-for-php)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Demo Application](#demo-application)
      - [Features](#features)
        - [Requirements](#requirements)
    - [Authentication](#authentication)
    - [Encryption](#encryption)
    - [Decryption](#decryption)
  - [Demo Application](#demo-application-1)
  - [Testing](#testing)
  - [Contributing](#contributing)
  - [License](#license)

## Installation

To install the Crittora SDK, you need to have Composer installed. Run the following command to install the dependencies:

```bash
composer install
```

## Usage

### Demo Application

https://github.com/Crittora/crittora-demo-php

This project is a demonstration of how to use the Crittora SDK for PHP. It provides a simple web interface for authentication, data encryption, and decryption using the Crittora SDK.

#### Features

- User authentication using environment variables.
- Data encryption and decryption.
- User-friendly web interface built with Bootstrap.

##### Requirements

- PHP 7.2 or higher
- Composer
- Access to the Crittora SDK

### Authentication

To authenticate a user, use the `authenticate` method of the `CrittoraSDK` class. This method requires a username and password and returns an array containing tokens.

```php
use Crittora\CrittoraSDK;

$sdk = new CrittoraSDK();
$authResponse = $sdk->authenticate('username', 'password');
```

### Encryption

To encrypt data, use the `encrypt` method. This method requires an ID token, the data to encrypt, and an optional array of permissions.

```php
$encryptedData = $sdk->encrypt($idToken, 'data to encrypt', ['read', 'write']);
```

### Decryption

To decrypt data, use the `decrypt` method. This method requires an ID token, the encrypted data, and an optional array of permissions.

```php
$decryptedData = $sdk->decrypt($idToken, $encryptedData, ['read']);
```

## Demo Application

The demo application provides a simple interface to test the authentication, encryption, and decryption functionalities of the SDK.

## Testing

To run the tests, use PHPUnit. Ensure you have PHPUnit installed and configured. Run the following command in the root directory:

```bash
  vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any improvements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.
