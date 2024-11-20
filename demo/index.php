<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crittora SDK Demo</title>
</head>
<body>
    <h1>Crittora SDK Demo</h1>
    <div>
        <h2>Authenticate</h2>
        <input type="text" id="username" placeholder="Username" value="testuser5">
        <input type="password" id="password" placeholder="Password" value="bObibRZt0*oyaU?p">
        <button onclick="authenticate()">Authenticate</button>
        <p id="tokenDisplay"></p>
    </div>
    <div>
        <h2>Encrypt Data</h2>
        <input type="text" id="data" placeholder="Data to encrypt">
        <button onclick="encryptData()">Encrypt</button>
        <p id="encryptMessage"></p>
    </div>
    <div>
        <h2>Encrypted Data</h2>
        <p id="encryptedDataDisplay"></p>
        <button onclick="copyEncryptedData()">Copy Encrypted Data</button>
    </div>
    <div>
        <h2>Decrypt Data</h2>
        <input type="text" id="encryptedData" placeholder="Encrypted data">
        <button onclick="decryptData()">Decrypt</button>
        <p id="decryptMessage"></p>
    </div>
    <script>
        let idToken = '';

        function authenticate() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch('authenticate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Authentication successful');
                    idToken = data.IdToken;
                    document.getElementById('tokenDisplay').textContent = `IdToken: ${idToken}`;
                } else {
                    console.error('Authentication failed:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function encryptData() {
            const data = document.getElementById('data').value;
            const permissions = ['read', 'write']; // Example permissions
            console.log('idToken: ', idToken);
            console.log('data: ', data);
            console.log('permissions: ', permissions);
            fetch('encrypt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ idToken, data, permissions })
            })
            .then(response => response.json())
            .then(data => {
                const messageElement = document.getElementById('encryptMessage');
                const encryptedDataDisplay = document.getElementById('encryptedDataDisplay');
                if (data.success) {
                    console.log('Encrypted data:', data.encryptedData);
                    messageElement.textContent = 'Encryption successful!';
                    encryptedDataDisplay.textContent = data.encryptedData; // Display encrypted data
                } else {
                    console.error('Encryption failed:', data.error);
                    messageElement.textContent = `Encryption failed: ${data.error}`;
                    encryptedDataDisplay.textContent = ''; // Clear display on failure
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('encryptMessage').textContent = `Error: ${error}`;
                document.getElementById('encryptedDataDisplay').textContent = ''; // Clear display on error
            });
        }

        function copyEncryptedData() {
            const encryptedData = document.getElementById('encryptedDataDisplay').textContent;
            navigator.clipboard.writeText(encryptedData).then(() => {
                alert('Encrypted data copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }

        function decryptData() {
            const encryptedData = document.getElementById('encryptedData').value;
            const permissions = ['read']; // Example permissions
            console.log('idToken: ', idToken);
            console.log('encryptedData: ', encryptedData);
            console.log('permissions: ', permissions);
            fetch('decrypt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ idToken, encryptedData, permissions })
            })
            .then(response => response.json())
            .then(data => {
                const messageElement = document.getElementById('decryptMessage');
                if (data.success) {
                    console.log('Decrypted data:', data.decryptedData);
                    messageElement.textContent = `Decryption successful! Data: ${data.decryptedData}`;
                } else {
                    console.error('Decryption failed:', data.error);
                    messageElement.textContent = `Decryption failed: ${data.error}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('decryptMessage').textContent = `Error: ${error}`;
            });
        }
    </script>
</body>
</html>