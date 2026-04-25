<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Login</title>
    <style>
        :root {
            --bg-1: #0b1b2b;
            --bg-2: #143a52;
            --card: rgba(8, 29, 48, 0.78);
            --line: rgba(173, 214, 255, 0.22);
            --txt: #ecf6ff;
            --muted: #a3c4df;
            --brand: #ffbe5c;
            --brand-2: #f59e0b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--txt);
            background:
                radial-gradient(48rem 30rem at 100% 0%, rgba(255, 190, 92, 0.18), transparent 65%),
                radial-gradient(45rem 30rem at 0% 100%, rgba(34, 197, 94, 0.17), transparent 62%),
                linear-gradient(140deg, var(--bg-1), var(--bg-2));
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .card {
            width: min(460px, 100%);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.33);
        }

        h1 {
            margin: 0;
            font-size: 1.9rem;
        }

        p {
            margin: 10px 0 18px;
            color: var(--muted);
        }

        .field {
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.86rem;
            color: #d7e7f5;
        }

        input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: rgba(19, 50, 80, 0.7);
            color: var(--txt);
            padding: 10px 12px;
            font-size: 0.95rem;
        }

        button {
            width: 100%;
            margin-top: 8px;
            border: 0;
            border-radius: 10px;
            padding: 11px 12px;
            font-weight: 700;
            background: linear-gradient(180deg, var(--brand), var(--brand-2));
            color: #3a2200;
            cursor: pointer;
        }

        .status {
            margin-top: 12px;
            min-height: 20px;
            color: #ffcece;
            font-size: 0.9rem;
        }

        .links {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        a { color: #9bd6ff; }
    </style>
</head>
<body>
<div class="card">
    <h1>Login</h1>
    <p>Masuk untuk mengakses modul sesuai tipe akun kamu: personal atau organization.</p>

    <form id="loginForm">
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" required placeholder="name@example.com">
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" required placeholder="******">
        </div>

        <button type="submit">Login</button>
        <div id="status" class="status"></div>
    </form>

    <div class="links">
        <a href="{{ route('auth.register') }}">Belum punya akun? Register</a>
        <a href="{{ route('auth.dashboard') }}">Buka Auth Dashboard</a>
    </div>
</div>

<script>
    const form = document.getElementById('loginForm');
    const statusEl = document.getElementById('status');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        statusEl.textContent = 'Loading...';

        const payload = {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
        };

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                statusEl.textContent = data.message || 'Login gagal';
                return;
            }

            const token = data.token || (data.data && data.data.token);
            const user = data.user || (data.data && data.data.user);

            if (token) {
                localStorage.setItem('auth_token', token);
            }

            statusEl.style.color = '#a9ffd4';
            statusEl.textContent = 'Login berhasil. Mengalihkan halaman...';

            if (user && user.role === 'organization') {
                window.location.href = '/campaigns';
                return;
            }

            window.location.href = '/donation-processing';
        } catch (error) {
            statusEl.textContent = 'Tidak bisa menghubungi server.';
        }
    });
</script>
</body>
</html>
