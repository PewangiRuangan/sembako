<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sembako TPA - Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- LOGIN SECTION -->
        <div id="login-section" class="card">
            <h1>Masuk</h1>
            <p>Sistem Pembagian Sembako</p>
            <input type="text" id="login-phone" placeholder="Nomor Handphone">
            <input type="password" id="login-pass" placeholder="Password">
            <button onclick="login()">Masuk</button>
            <button class="secondary-btn" onclick="toggleForm('register')">Belum punya akun? Daftar</button>
        </div>

        <!-- REGISTER SECTION -->
        <div id="register-section" class="card hidden">
            <h1>Daftar</h1>
            <input type="text" id="reg-name" placeholder="Nama Lengkap">
            <input type="text" id="reg-phone" placeholder="Nomor Handphone">
            <input type="password" id="reg-pass" placeholder="Password">
            <select id="reg-type">
                <option value="">-- Pilih Status Tempat Tinggal --</option>
                <option value="kos">Anak Kos</option>
                <option value="kontrakan">Kontrakan</option>
                <option value="asrama">Asrama Aceh</option>
            </select>
            <button onclick="register()">Daftar</button>
            <button class="secondary-btn" onclick="toggleForm('login')">Sudah punya akun? Masuk</button>
        </div>
    </div>

    <script>
        function toggleForm(show) {
            if(show === 'register') {
                document.getElementById('login-section').classList.add('hidden');
                document.getElementById('register-section').classList.remove('hidden');
            } else {
                document.getElementById('register-section').classList.add('hidden');
                document.getElementById('login-section').classList.remove('hidden');
            }
        }

        async function register() {
            const name = document.getElementById('reg-name').value;
            const phone = document.getElementById('reg-phone').value;
            const pass = document.getElementById('reg-pass').value;
            const type = document.getElementById('reg-type').value;

            const res = await fetch('api/register.php', {
                method: 'POST',
                body: JSON.stringify({
                    full_name: name,
                    phone_number: phone,
                    password: pass,
                    residence_type: type
                })
            });
            const data = await res.json();
            alert(data.message);
            if(data.success) toggleForm('login');
        }

        async function login() {
            const phone = document.getElementById('login-phone').value;
            const pass = document.getElementById('login-pass').value;

            const res = await fetch('api/login.php', {
                method: 'POST',
                body: JSON.stringify({
                    phone_number: phone,
                    password: pass
                })
            });
            const data = await res.json();
            if(data.success) {
                localStorage.setItem('user', JSON.stringify(data.user));
                window.location.href = 'dashboard.php';
            } else {
                alert(data.message);
            }
        }
    </script>
</body>
</html>