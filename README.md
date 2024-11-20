# Crittora SDK for PHP

The Crittora SDK for PHP provides a simple interface for authentication, encryption, and decryption of data using the Crittora API. This document will guide you through the setup and usage of the SDK, as well as running the demo application.

## Table of Contents

- [Crittora SDK for PHP](#crittora-sdk-for-php)
  - [Table of Contents](#table-of-contents)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Authentication](#authentication)
    - [Encryption](#encryption)
    - [Decryption](#decryption)
  - [Demo Application](#demo-application)
    - [Running the Demo](#running-the-demo)
  - [Testing](#testing)
  - [Contributing](#contributing)
  - [License](#license)

## Installation

To install the Crittora SDK, you need to have Composer installed. Run the following command to install the SDK:

```bash
composer require wutif/crittora
```

## Usage

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

### Running the Demo

1. **Setup Environment Variables**: Ensure you have a `.env` file in the root directory with the necessary environment variables. Refer to the `ConfigManager` class for required variables.

2. **Start a Local Server**: Navigate to the `demo` directory and start a PHP server:

   ```bash
   php -S localhost:8000
   ```

3. **Access the Demo**: Open your browser and go to `http://localhost:8000/demo/index.php` to access the demo interface.

4. **Authenticate**: Enter the username and password, then click "Authenticate" to obtain an ID token.

5. **Encrypt Data**: Enter the data you wish to encrypt and click "Encrypt".

6. **Decrypt Data**: Enter the encrypted data and click "Decrypt" to retrieve the original data.

## Testing

To run the tests, use PHPUnit. Ensure you have PHPUnit installed and configured. Run the following command in the root directory:

```bash
  vendor/bin/phpunit
```

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request for any improvements or bug fixes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.
