<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sembako</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2>Halo, <span id="user-name">User</span></h2>
            <p>Status: <b id="user-status">...</b></p>

            <div id="eligible-content" class="hidden">
                <div class="alert alert-success">Anda Berhak Mendapatkan Sembako</div>
                <p>Tunjukkan QR Code ini kepada panitia:</p>
                <div id="qrcode"></div>
            </div>

            <div id="not-eligible-content" class="hidden">
                <div class="alert alert-error">Maaf, Anda tidak memenuhi syarat (Khusus Kos/Kontrakan).</div>
            </div>

            <button class="secondary-btn" onclick="logout()" style="margin-top:20px;">Keluar</button>
        </div>
    </div>

    <script>
        const user = JSON.parse(localStorage.getItem('user'));
        if (!user) window.location.href = 'index.php';

        document.getElementById('user-name').innerText = user.full_name;
        document.getElementById('user-status').innerText = user.residence_type.toUpperCase();

        if (user.residence_type === 'kos' || user.residence_type === 'kontrakan') {
            document.getElementById('eligible-content').classList.remove('hidden');
            // Generate QR Code containing User ID
            new QRCode(document.getElementById("qrcode"), {
                text: JSON.stringify({ user_id: user.id, name: user.full_name }),
                width: 128,
                height: 128
            });
        } else {
            document.getElementById('not-eligible-content').classList.remove('hidden');
        }

        function logout() {
            localStorage.removeItem('user');
            window.location.href = 'index.php';
        }
    </script>
</body>

</html>