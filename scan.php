<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Scanner</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>

<body>
    <div class="container" style="max-width: 600px;">
        <div class="card">
            <h1>Admin Scanner</h1>
            <div id="reader" style="width: 100%;"></div>
            <div id="result" class="alert hidden"></div>
            <button onclick="startScanner()">Buka Kamera</button>
            <button class="secondary-btn" onclick="location.href='index.php'">Kembali</button>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;

        function onScanSuccess(decodedText, decodedResult) {
            // Handle the scanned code as you like, for example:
            console.log(`Code matched = ${decodedText}`, decodedResult);

            try {
                const data = JSON.parse(decodedText);
                if (data.user_id) {
                    processClaim(data.user_id);
                    // Stop scanning temporarily or permanently
                    html5QrcodeScanner.clear();
                }
            } catch (e) {
                alert("QR Code tidak valid!");
            }
        }

        async function processClaim(userId) {
            const resultDiv = document.getElementById('result');
            resultDiv.innerText = "Memproses...";
            resultDiv.className = "alert";
            resultDiv.classList.remove('hidden');

            try {
                const res = await fetch('api/process_scan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId })
                });
                const data = await res.json();

                resultDiv.innerText = data.message;
                if (data.success) {
                    resultDiv.classList.add('alert-success');
                    resultDiv.classList.remove('alert-error');
                } else {
                    resultDiv.classList.add('alert-error');
                    resultDiv.classList.remove('alert-success');
                }
            } catch (err) {
                console.error(err);
                resultDiv.innerText = "Error koneksi server.";
            }

            // Allow rescanning after 3 seconds
            setTimeout(() => {
                location.reload();
            }, 3000);
        }

        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                /* verbose= */ false
            );
            html5QrcodeScanner.render(onScanSuccess, (error) => {
                // handle scan failure, usually better to ignore and keep scanning.
                console.warn(`Code scan error = ${error}`);
            });
        }
    </script>
</body>

</html>