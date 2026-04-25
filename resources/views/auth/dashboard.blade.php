<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Control Center</title>
    <style>
        :root {
            --bg-1: #130f2f;
            --bg-2: #1f4c73;
            --card: rgba(20, 28, 58, 0.78);
            --line: rgba(189, 228, 255, 0.2);
            --txt: #edf5ff;
            --muted: #abc7e2;
            --brand: #ffd166;
            --brand-2: #fca311;
            --accent: #74f2ce;
            --danger: #ff9ca5;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Trebuchet MS", sans-serif;
            color: var(--txt);
            background:
                radial-gradient(42rem 30rem at 95% -5%, rgba(116, 242, 206, 0.2), transparent 65%),
                radial-gradient(40rem 28rem at -5% 102%, rgba(255, 209, 102, 0.2), transparent 62%),
                linear-gradient(140deg, var(--bg-1), var(--bg-2));
            padding: 20px;
        }

        .wrap {
            width: min(1100px, 100%);
            margin: 0 auto;
        }

        h1 {
            margin: 0 0 8px;
            letter-spacing: 0.2px;
        }

        p {
            color: var(--muted);
            margin: 0 0 18px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 16px;
        }

        .span-2 {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.86rem;
            color: #d7eff8;
        }

        input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: rgba(15, 53, 74, 0.8);
            color: var(--txt);
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: rgba(15, 53, 74, 0.8);
            color: var(--txt);
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            border: 0;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            background: linear-gradient(180deg, var(--brand), var(--brand-2));
            color: #3a2000;
            cursor: pointer;
        }

        .ghost {
            background: rgba(125, 211, 252, 0.2);
            color: #c8edff;
            border: 1px solid var(--line);
        }

        .success {
            background: rgba(116, 242, 206, 0.2);
            color: #d8fff5;
            border: 1px solid rgba(116, 242, 206, 0.35);
        }

        .danger {
            background: rgba(255, 156, 165, 0.2);
            color: #ffdce0;
            border: 1px solid rgba(255, 156, 165, 0.35);
        }

        pre {
            margin: 0;
            background: rgba(0, 0, 0, 0.22);
            border: 1px solid rgba(160, 226, 255, 0.16);
            border-radius: 10px;
            padding: 10px;
            color: #e9fbff;
            min-height: 220px;
            overflow: auto;
            font-size: 0.82rem;
        }

        .status {
            margin-top: 8px;
            min-height: 18px;
            color: var(--muted);
            font-size: 0.88rem;
        }

        .full { grid-column: 1 / -1; }

        .links {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        a { color: #9bd6ff; }

        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 800px) {
            .grid { grid-template-columns: 1fr; }
            .span-2 { grid-column: auto; }
            .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Auth Control Center</h1>
    <p>Panel end-to-end untuk uji register, login, refresh, logout, verify organization, dan history donation.</p>

    <div class="grid">
        <div class="card span-2">
            <h3>Register</h3>
            <div class="row">
                <div>
                    <label for="regName">Name</label>
                    <input id="regName" type="text" placeholder="Nama user">
                </div>
                <div>
                    <label for="regEmail">Email</label>
                    <input id="regEmail" type="email" placeholder="email@example.com">
                </div>
            </div>
            <div class="row">
                <div>
                    <label for="regPassword">Password</label>
                    <input id="regPassword" type="password" placeholder="minimal 6 karakter">
                </div>
                <div>
                    <label for="regRole">Role</label>
                    <select id="regRole">
                        <option value="personal">personal</option>
                        <option value="organization">organization</option>
                    </select>
                </div>
            </div>
            <div class="actions">
                <button onclick="runRegister()">Register User</button>
            </div>
            <div id="registerStatus" class="status"></div>
        </div>

        <div class="card">
            <h3>Login</h3>
            <label for="loginEmail">Email</label>
            <input id="loginEmail" type="email" placeholder="email@example.com">

            <label for="loginPassword">Password</label>
            <input id="loginPassword" type="password" placeholder="password">

            <div class="actions">
                <button onclick="runLogin()">Login</button>
            </div>
            <div id="loginStatus" class="status"></div>
        </div>

        <div class="card span-2">
            <h3>Token and Protected Calls</h3>
            <label for="token">JWT Token</label>
            <input id="token" type="text" placeholder="Token dari login">

            <label for="verifyId">Organization User ID (untuk verify admin)</label>
            <input id="verifyId" type="number" min="1" value="1">

            <div class="actions">
                <button onclick="runRefresh()">Refresh Token</button>
                <button onclick="runLogout()">Logout</button>
                <button onclick="runVerify()">Verify Organization</button>
                <button class="success" onclick="runDonationHistory()">Donation History</button>
                <button class="ghost" onclick="saveToken()">Simpan Token</button>
                <button class="ghost" onclick="loadToken()">Load Token</button>
                <button class="danger" onclick="clearOutput()">Clear Output</button>
            </div>
        </div>

        <div class="card">
            <h3>Response</h3>
            <pre id="output">Response akan tampil di sini...</pre>
        </div>

        <div class="card full">
            <strong>Quick links:</strong>
            <div class="links">
                <a href="{{ route('auth.login') }}">Login</a>
                <a href="{{ route('auth.register') }}">Register</a>
                <a href="/">Main Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script>
    const output = document.getElementById('output');

    function setStatus(id, message, ok = true) {
        const el = document.getElementById(id);
        el.textContent = message;
        el.style.color = ok ? '#b9ffe5' : '#ffd0d6';
    }

    function getToken() {
        return document.getElementById('token').value.trim();
    }

    function setOutput(value) {
        output.textContent = typeof value === 'string' ? value : JSON.stringify(value, null, 2);
    }

    async function callApi(url, method = 'POST', payload = null) {
        const token = getToken();
        const headers = { 'Content-Type': 'application/json' };

        if (token) {
            headers.Authorization = `Bearer ${token}`;
        }

        const response = await fetch(url, {
            method,
            headers,
            body: payload ? JSON.stringify(payload) : null,
        });
        const data = await response.json();
        setOutput({ status: response.status, data });

        if (data.token) {
            document.getElementById('token').value = data.token;
        } else if (data.data && data.data.token) {
            document.getElementById('token').value = data.data.token;
        }

        return { response, data };
    }

    async function runRegister() {
        const payload = {
            name: document.getElementById('regName').value,
            email: document.getElementById('regEmail').value,
            password: document.getElementById('regPassword').value,
            role: document.getElementById('regRole').value,
        };

        try {
            const { response } = await callApi('/api/auth/register', 'POST', payload);
            setStatus('registerStatus', response.ok ? 'Register berhasil' : 'Register gagal', response.ok);
        } catch (error) {
            setStatus('registerStatus', 'Request register gagal', false);
        }
    }

    async function runLogin() {
        const payload = {
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value,
        };

        try {
            const { response, data } = await callApi('/api/auth/login', 'POST', payload);

            const token = data.token || (data.data && data.data.token);
            if (response.ok && token) {
                document.getElementById('token').value = token;
                localStorage.setItem('auth_token', token);
                setStatus('loginStatus', 'Login berhasil, token disimpan', true);
                return;
            }

            setStatus('loginStatus', 'Login gagal', false);
        } catch (error) {
            setStatus('loginStatus', 'Request login gagal', false);
        }
    }

    async function runRefresh() {
        try {
            await callApi('/api/auth/refresh');
        } catch (error) {
            setOutput('Request refresh gagal');
        }
    }

    async function runLogout() {
        try {
            await callApi('/api/auth/logout');
        } catch (error) {
            setOutput('Request logout gagal');
        }
    }

    async function runVerify() {
        const verifyId = document.getElementById('verifyId').value || '1';
        try {
            await callApi(`/api/auth/verify/${verifyId}`);
        } catch (error) {
            setOutput('Request verify gagal');
        }
    }

    async function runDonationHistory() {
        try {
            await callApi('/api/donations/history', 'GET');
        } catch (error) {
            setOutput('Request donation history gagal');
        }
    }

    function saveToken() {
        localStorage.setItem('auth_token', getToken());
        setOutput('Token disimpan ke localStorage');
    }

    function loadToken() {
        const token = localStorage.getItem('auth_token') || '';
        document.getElementById('token').value = token;
        setOutput(token ? 'Token dimuat dari localStorage' : 'Belum ada token tersimpan');
    }

    function clearOutput() {
        setOutput('Response akan tampil di sini...');
    }

    loadToken();
</script>
</body>
</html>
