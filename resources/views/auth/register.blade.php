<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Register</title>
    <style>
        :root {
            --bg-1: #1f1235;
            --bg-2: #3b185f;
            --card: rgba(42, 17, 67, 0.78);
            --line: rgba(238, 201, 255, 0.22);
            --txt: #fbf0ff;
            --muted: #d8b9e8;
            --brand: #7ef8b1;
            --brand-2: #34d399;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--txt);
            background:
                radial-gradient(42rem 30rem at 100% 0%, rgba(126, 248, 177, 0.16), transparent 64%),
                radial-gradient(40rem 28rem at 0% 100%, rgba(251, 146, 60, 0.2), transparent 60%),
                linear-gradient(145deg, var(--bg-1), var(--bg-2));
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .card {
            width: min(500px, 100%);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.33);
        }

        h1 { margin: 0; font-size: 1.9rem; }
        p { margin: 10px 0 18px; color: var(--muted); }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .field { margin-bottom: 12px; }
        .field.full { grid-column: 1 / -1; }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.86rem;
            color: #f3dafc;
        }

        input, select {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: rgba(73, 28, 104, 0.7);
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
            color: #05331e;
            cursor: pointer;
        }

        .status {
            margin-top: 12px;
            min-height: 20px;
            color: #ffd2e1;
            font-size: 0.9rem;
        }

        .links {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        a { color: #a6f7c7; }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Register</h1>
    <p>Buat akun personal atau organization untuk akses modul crowdfunding.</p>

    <form id="registerForm">
        <div class="grid">
            <div class="field full">
                <label for="name">Nama</label>
                <input id="name" type="text" required placeholder="Nama lengkap">
            </div>

            <div class="field full">
                <label for="email">Email</label>
                <input id="email" type="email" required placeholder="name@example.com">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" required minlength="6">
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role">
                    <option value="personal">personal</option>
                    <option value="organization">organization</option>
                </select>
            </div>
        </div>

        <button type="submit">Register</button>
        <div id="status" class="status"></div>
    </form>

    <div class="links">
        <a href="{{ route('auth.login') }}">Sudah punya akun? Login</a>
        <a href="{{ route('auth.dashboard') }}">Buka Auth Dashboard</a>
    </div>
</div>

<script>
    const form = document.getElementById('registerForm');
    const statusEl = document.getElementById('status');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        statusEl.textContent = 'Loading...';

        const payload = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value,
        };

        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                const message = data.message || data.error || 'Register gagal';
                statusEl.textContent = typeof message === 'string' ? message : JSON.stringify(message);
                return;
            }

            statusEl.style.color = '#b8ffd7';
            statusEl.textContent = 'Register berhasil. Mengalihkan ke halaman login...';
            form.reset();

            setTimeout(() => {
                window.location.href = '/auth/login';
            }, 800);
        } catch (error) {
            statusEl.textContent = 'Tidak bisa menghubungi server.';
        }
    });
</script>
</body>
</html>
