<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Campaign Auth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #06152f;
            --bg-accent: #0f2a56;
            --bg-soft: #13346b;
            --card: rgba(30, 47, 80, 0.62);
            --card-strong: rgba(43, 62, 98, 0.74);
            --line: rgba(205, 223, 255, 0.18);
            --line-soft: rgba(205, 223, 255, 0.1);
            --text-main: #ecf3ff;
            --text-muted: #b2c2df;
            --chip: rgba(124, 162, 236, 0.18);
            --primary: #71e6c4;
            --primary-dark: #4bcaa4;
            --ring: rgba(113, 230, 196, 0.24);
        }

        * {
        select option {
            color: #0f2348;
            background: #eef4ff;
        }
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(56rem 40rem at 88% -8%, rgba(53, 109, 196, 0.46), transparent 68%),
                radial-gradient(48rem 48rem at -14% 102%, rgba(26, 57, 117, 0.65), transparent 72%),
                linear-gradient(135deg, var(--bg-main), var(--bg-accent) 52%, #0b1f45 100%);
            padding: 2.1rem 1rem 2.6rem;
            overflow-x: hidden;
        }

        .shell {
        select option {
            color: #0f2348;
            background: #eef4ff;
        }
            width: min(1050px, 100%);
            margin: 0 auto;
            animation: fade-up 700ms ease-out;
        }

        .hero {
            margin-bottom: 1.1rem;
            animation: fade-up 700ms ease-out;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 3.2rem);
            line-height: 1.07;
            font-weight: 800;
            max-width: 18ch;
            letter-spacing: -0.015em;
        }

        .hero p {
            margin: 0.8rem 0 0;
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 56ch;
        }

        .chips {
            margin-top: 1rem;
            display: flex;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .chip {
            border: 1px solid var(--line);
            color: #d5e2fb;
            background: var(--chip);
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.76rem;
            font-weight: 600;
            backdrop-filter: blur(5px);
        }

        .content {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 0.95rem;
        }

        .card {
            background: linear-gradient(165deg, var(--card-strong), var(--card));
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 14px 40px rgba(2, 9, 24, 0.42);
            backdrop-filter: blur(8px);
        }

        .register-card {
            padding: 1.25rem;
            animation: fade-up 760ms ease-out;
            animation-delay: 60ms;
            animation-fill-mode: both;
        }

        .info-card {
            padding: 1.25rem;
            animation: fade-up 800ms ease-out;
            animation-delay: 140ms;
            animation-fill-mode: both;
        }

        .register-title {
            margin: 0;
            font-size: 1.7rem;
            font-weight: 750;
        }

        .register-subtitle {
            margin: 0.4rem 0 1rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            max-width: 52ch;
            line-height: 1.5;
        }

        .field {
            display: grid;
            gap: 0.42rem;
            margin-bottom: 0.75rem;
        }

        label {
            color: #c9d8f4;
            font-size: 0.82rem;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            background: rgba(137, 166, 221, 0.14);
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            color: var(--text-main);
            padding: 0.84rem 0.9rem;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 140ms ease, background 140ms ease, box-shadow 140ms ease;
        }

        select {
            appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, #b2c2df 50%), linear-gradient(135deg, #b2c2df 50%, transparent 50%);
            background-position: calc(100% - 18px) calc(50% + 1px), calc(100% - 12px) calc(50% + 1px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 2.2rem;
        }

        input::placeholder {
            color: #9eb2d7;
        }

        input:focus,
        select:focus {
            outline: none;
            background: rgba(137, 166, 221, 0.2);
            border-color: #8ac4ff;
            box-shadow: 0 0 0 4px var(--ring);
        }

        button {
            border: none;
            border-radius: 12px;
            padding: 0.78rem 1rem;
            font-size: 0.95rem;
            font-weight: 750;
            color: #063028;
            background: linear-gradient(180deg, #86f2d1 0%, var(--primary) 100%);
            cursor: pointer;
            transition: transform 140ms ease, filter 140ms ease, box-shadow 140ms ease;
            box-shadow: 0 8px 18px rgba(56, 205, 163, 0.28);
        }

        button:hover {
            transform: translateY(-1px) scale(1.01);
            filter: brightness(1.04);
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .action-row {
            margin-top: 0.95rem;
            display: flex;
            gap: 0.7rem;
            align-items: center;
            flex-wrap: wrap;
        }

        a {
            color: #89c7ff;
            font-weight: 600;
            text-underline-offset: 3px;
        }

        .notice {
            margin-top: 0.6rem;
            min-height: 1.25rem;
            font-size: 0.86rem;
            color: var(--text-muted);
        }

        .notice.error {
            color: #ff9382;
        }

        .notice.success {
            color: #8af2cf;
        }

        .info-card h3 {
            margin: 0;
            font-size: 1.42rem;
            font-weight: 750;
        }

        .info-card p {
            margin: 0.45rem 0 0.9rem;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .metric {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 0.68rem 0.8rem;
            background: rgba(138, 171, 232, 0.13);
            margin-bottom: 0.62rem;
        }

        .metric span {
            display: block;
            color: #aac0e6;
            font-size: 0.72rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
            letter-spacing: 0.03em;
        }

        .metric strong {
            font-size: 1.05rem;
            line-height: 1.35;
            display: block;
        }

        .updates {
            margin-top: 0.78rem;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            background: rgba(26, 41, 72, 0.55);
            padding: 0.72rem;
        }

        .updates p {
            margin: 0;
            font-size: 0.82rem;
            padding: 0.45rem 0.52rem;
            border-radius: 8px;
            background: rgba(161, 185, 235, 0.12);
            color: #dbe7ff;
        }

        .updates p + p {
            margin-top: 0.38rem;
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 930px) {
            .content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .register-card,
            .info-card {
                padding: 1rem;
            }

            .hero p {
                font-size: 0.93rem;
            }

            .hero h1 {
                font-size: 1.75rem;
            }

            .chip {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
<main class="shell">
    <section class="hero">
        <h1>Modul User & Authentication</h1>
        <p>Buat akun donor atau organizer untuk masuk ke alur JWT yang sama, sehingga halaman login dan dashboard memakai gaya yang seragam.</p>
        <div class="chips">
            <span class="chip">JWT Login</span>
            <span class="chip">Donation History</span>
            <span class="chip">Organizer Verification</span>
        </div>
    </section>

    <section class="content">
        <article class="card register-card">
            <h2 class="register-title">Register Akun</h2>
            <p class="register-subtitle">Lengkapi data di bawah untuk membuat akun baru dan langsung menggunakan fitur platform crowdfunding.</p>

            <form id="registerForm">
                @csrf
                <div class="field">
                    <label for="name">Nama</label>
                    <input id="name" type="text" name="name" placeholder="Nama lengkap" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" placeholder="contoh@email.com" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Masukkan password" required>
                </div>

                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="donor">Donor</option>
                        <option value="organizer">Organizer</option>
                    </select>
                </div>

                <div class="action-row">
                    <button id="submitBtn" type="submit">Buat Akun</button>
                    <a href="/login">Sudah punya akun? Masuk</a>
                </div>

                <p id="notice" class="notice"></p>
            </form>
        </article>

        <aside class="card info-card">
            <h3>Register Live Total</h3>
            <p>Ringkasan alur pendaftaran untuk role donor dan organizer yang siap digunakan.</p>

            <div class="metric">
                <span>Role Donor</span>
                <strong>Bisa mendukung campaign dan memantau histori donasi.</strong>
            </div>

            <div class="metric">
                <span>Role Organizer</span>
                <strong>Bisa membuat, mengelola, dan memperbarui campaign.</strong>
            </div>

            <div class="updates">
                <p>Gunakan email aktif untuk verifikasi dan notifikasi campaign.</p>
                <p>Setelah registrasi, kamu bisa langsung lanjut ke proses login.</p>
            </div>
        </aside>
    </section>
</main>

<script>
document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const notice = document.getElementById("notice");
    const submitBtn = document.getElementById("submitBtn");
    const formData = new FormData(this);
    const payload = {
        name: formData.get("name"),
        email: formData.get("email"),
        password: formData.get("password"),
        role: formData.get("role"),
    };

    notice.className = "notice";
    notice.textContent = "Memproses pendaftaran...";
    submitBtn.disabled = true;

    try {
        const res = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok) {
            notice.className = "notice success";
            notice.textContent = data.message || "Registrasi berhasil. Mengarahkan ke halaman login...";
            setTimeout(() => {
                window.location.href = "/login";
            }, 1200);
            return;
        }

        notice.className = "notice error";
        if (data.errors && typeof data.errors === 'object') {
            const firstError = Object.values(data.errors)[0];
            notice.textContent = Array.isArray(firstError) ? firstError[0] : "Registrasi gagal. Cek kembali data yang kamu isi.";
        } else {
            notice.textContent = data.message || "Registrasi gagal. Cek kembali data yang kamu isi.";
        }
    } catch (error) {
        notice.className = "notice error";
        notice.textContent = "Terjadi gangguan jaringan. Coba lagi.";
    } finally {
        submitBtn.disabled = false;
    }
});
</script>

</body>
</html>
