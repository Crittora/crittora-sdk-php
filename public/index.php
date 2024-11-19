<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Crittora\CrittoraSDK;
use Dotenv\Dotenv;

session_start();

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize the SDK
try {
    $sdk = new CrittoraSDK();
} catch (Exception $e) {
    die('SDK initialization failed: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crittora Encryption App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <div class="pure-g">
        <div class="pure-u-1-3"></div>
        <div class="pure-u-1-3">
            <div style="margin: 1em">
                <h1>Crittora Encryption Service</h1>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['auth_status'])): ?>
                    <div class="<?php echo $_SESSION['auth_status'] === 'success' ? 'success' : 'error'; ?>">
                        <?php 
                            echo htmlspecialchars($_SESSION['auth_message']); 
                            // Clear the message after displaying
                            unset($_SESSION['auth_status']);
                            unset($_SESSION['auth_message']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Add alerts for encryption/decryption operations -->
                <?php if (isset($_SESSION['encrypt_error'])): ?>
                    <div class="error">
                        <?php 
                            echo htmlspecialchars($_SESSION['encrypt_error']); 
                            unset($_SESSION['encrypt_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['decrypt_error'])): ?>
                    <div class="error">
                        <?php 
                            echo htmlspecialchars($_SESSION['decrypt_error']); 
                            unset($_SESSION['decrypt_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Authentication Form -->
                <?php if (!isset($_SESSION['auth_token'])): ?>
                    <form class="pure-form pure-form-stacked" action="/auth.php" method="POST">
                        <fieldset>
                            <legend>Authentication</legend>
                            
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                            
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                            
                            <button type="submit" class="pure-button pure-button-primary">Login</button>
                        </fieldset>
                    </form>
                <?php endif; ?>

                <!-- Encryption Form -->
                <form method="POST" action="encrypt.php" id="encryptForm" class="pure-form pure-form-stacked" style="margin-bottom: 2em">
                    <fieldset>
                        <legend>Encrypt Data</legend>
                        <textarea name="data" id="dataToEncrypt" placeholder="Enter data to encrypt" required></textarea>
                        <button type="submit" class="pure-button pure-button-primary">Encrypt</button>
                        <div class="results" <?php echo isset($_SESSION['encrypted_data']) ? '' : 'style="display: none"'; ?>>
                            <label>Encrypted Result:</label>
                            <div class="encrypted-data-container">
                                <pre id="encryptedResult"><?php echo isset($_SESSION['encrypted_data']) ? $_SESSION['encrypted_data'] : ''; ?></pre>
                                <button id="copyButton" class="copy-button" title="Copy to clipboard">Copy</button>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <!-- Decryption Form -->
                <form method="POST" action="decrypt.php" id="decryptForm" class="pure-form pure-form-stacked">
                    <fieldset>
                        <legend>Decrypt Data</legend>
                        <textarea name="data" id="dataToDecrypt" placeholder="Enter data to decrypt" required></textarea>
                        <button type="submit" class="pure-button pure-button-primary">Decrypt</button>
                        <div class="results" <?php echo isset($_SESSION['decrypted_data']) ? '' : 'style="display: none"'; ?>>
                            <label>Decrypted Result:</label>
                            <div class="encrypted-data-container">
                                <pre id="decryptedResult"><?php echo isset($_SESSION['decrypted_data']) ? $_SESSION['decrypted_data'] : ''; ?></pre>
                                <button id="decryptCopyButton" class="copy-button" title="Copy to clipboard">Copy</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <script src="/js/app.js"></script>
</body>
</html> 