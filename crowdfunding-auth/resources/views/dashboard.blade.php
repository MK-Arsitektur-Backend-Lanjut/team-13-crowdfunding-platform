<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Campaign Auth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #06152f;
            --bg-accent: #0f2a56;
            --card: rgba(30, 47, 80, 0.62);
            --card-strong: rgba(43, 62, 98, 0.74);
            --line: rgba(205, 223, 255, 0.18);
            --line-soft: rgba(205, 223, 255, 0.1);
            --text-main: #ecf3ff;
            --text-muted: #b2c2df;
            --chip: rgba(124, 162, 236, 0.18);
            --primary: #71e6c4;
            --danger: #ff9382;
            --warning: #ffd37c;
            --ring: rgba(113, 230, 196, 0.24);
        }

        * {
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
            width: min(1100px, 100%);
            margin: 0 auto;
            animation: fade-up 700ms ease-out;
        }

        .hero {
            margin-bottom: 1rem;
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 3rem);
            line-height: 1.07;
            font-weight: 800;
            max-width: 20ch;
            letter-spacing: -0.015em;
        }

        .hero p {
            margin: 0.8rem 0 0;
            color: var(--text-muted);
            font-size: 1rem;
            max-width: 58ch;
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

        .tab-btn {
            border: 1px solid var(--line);
            color: #d5e2fb;
            background: var(--chip);
            border-radius: 999px;
            padding: 0.35rem 0.7rem;
            font-size: 0.76rem;
            font-weight: 700;
            backdrop-filter: blur(5px);
            cursor: pointer;
            transition: transform 130ms ease, filter 130ms ease, border-color 130ms ease, background 130ms ease;
        }

        .tab-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.06);
        }

        .tab-btn.active {
            background: rgba(113, 230, 196, 0.2);
            color: #dffff4;
            border-color: rgba(113, 230, 196, 0.65);
            box-shadow: 0 6px 16px rgba(56, 205, 163, 0.22);
        }

        .content {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.95rem;
        }

        .tab-panel {
            grid-column: 1 / -1;
            display: none;
        }

        .tab-panel.active {
            display: block;
            animation: fade-up 280ms ease-out;
        }

        .span-2 {
            grid-column: 1 / -1;
        }

        .card {
            background: linear-gradient(165deg, var(--card-strong), var(--card));
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 14px 40px rgba(2, 9, 24, 0.42);
            backdrop-filter: blur(8px);
            padding: 1.2rem;
        }

        .card h2,
        .card h3 {
            margin: 0;
            font-size: 1.38rem;
            font-weight: 750;
        }

        .sub {
            margin: 0.45rem 0 1rem;
            color: var(--text-muted);
            font-size: 0.9rem;
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

        input {
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

        input:focus {
            outline: none;
            background: rgba(137, 166, 221, 0.2);
            border-color: #8ac4ff;
            box-shadow: 0 0 0 4px var(--ring);
        }

        .actions {
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 0.2rem;
        }

        button,
        .btn-link {
            border: none;
            border-radius: 12px;
            padding: 0.78rem 1rem;
            font-size: 0.92rem;
            font-weight: 750;
            cursor: pointer;
            transition: transform 140ms ease, filter 140ms ease, box-shadow 140ms ease;
        }

        button {
            color: #063028;
            background: linear-gradient(180deg, #86f2d1 0%, var(--primary) 100%);
            box-shadow: 0 8px 18px rgba(56, 205, 163, 0.28);
        }

        .btn-link {
            color: #dce8ff;
            background: rgba(124, 162, 236, 0.2);
            border: 1px solid var(--line);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        button:hover,
        .btn-link:hover {
            transform: translateY(-1px) scale(1.01);
            filter: brightness(1.04);
        }

        .status {
            margin-top: 0.75rem;
            min-height: 1.3rem;
            color: var(--text-muted);
            font-size: 0.86rem;
        }

        .status.ok {
            color: #8af2cf;
        }

        .status.err {
            color: var(--danger);
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
            font-size: 1.25rem;
            line-height: 1.25;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.62rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 0.32rem 0.66rem;
            background: rgba(124, 162, 236, 0.16);
            color: #dbe7ff;
            font-size: 0.74rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .panel {
            margin-top: 0.78rem;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            background: rgba(26, 41, 72, 0.55);
            padding: 0.72rem;
        }

        .table-wrap {
            margin-top: 0.84rem;
            border: 1px solid var(--line-soft);
            border-radius: 12px;
            overflow: hidden;
            background: rgba(26, 41, 72, 0.55);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
        }

        thead th {
            background: rgba(145, 176, 229, 0.12);
            color: #d7e4fb;
            text-align: left;
            padding: 0.7rem 0.72rem;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-bottom: 1px solid var(--line-soft);
        }

        tbody td {
            padding: 0.72rem;
            border-bottom: 1px solid rgba(205, 223, 255, 0.08);
            color: #e3ebfb;
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .muted-cell {
            color: var(--text-muted);
        }

        .row-actions {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .small-btn {
            border-radius: 10px;
            padding: 0.55rem 0.72rem;
            font-size: 0.8rem;
            box-shadow: none;
        }

        .small-btn.secondary {
            color: #e5ecfb;
            background: rgba(124, 162, 236, 0.18);
            border: 1px solid var(--line);
        }

        .small-btn.danger {
            color: #ffe3de;
            background: rgba(255, 147, 130, 0.14);
            border: 1px solid rgba(255, 147, 130, 0.3);
        }

        .empty-state {
            padding: 0.96rem;
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .pager {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 0.75rem;
        }

        .pager-info {
            color: var(--text-muted);
            font-size: 0.82rem;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.62rem;
        }

        .filters .field {
            margin-bottom: 0;
        }

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
            appearance: none;
            background-image: linear-gradient(45deg, transparent 50%, #b2c2df 50%), linear-gradient(135deg, #b2c2df 50%, transparent 50%);
            background-position: calc(100% - 18px) calc(50% + 1px), calc(100% - 12px) calc(50% + 1px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 2.2rem;
        }

        select option {
            color: #0f2348;
            background: #eef4ff;
        }

        select option:checked,
        select option:hover {
            background: #d8e7ff;
        }

        .note {
            margin-top: 0.72rem;
            padding: 0.72rem 0.8rem;
            border: 1px solid rgba(255, 211, 124, 0.22);
            border-radius: 12px;
            background: rgba(255, 211, 124, 0.08);
            color: #ffe7b3;
            font-size: 0.84rem;
            line-height: 1.5;
        }

        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 0.8rem;
            color: #dbe7ff;
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

            .span-2 {
                grid-column: auto;
            }

            .metric-row,
            .filters {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .card {
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
        <p>Halaman ini menggabungkan tiga fitur modul 3 dalam satu tampilan yang seragam: login JWT, riwayat donasi pribadi, dan verifikasi akun organisasi.</p>
        <div class="chips" role="tablist" aria-label="Pilih fitur modul 3">
            <button type="button" class="tab-btn active" data-tab="jwt" aria-selected="true">JWT Login</button>
            <button type="button" class="tab-btn" data-tab="history" aria-selected="false">Donation History</button>
            <button type="button" class="tab-btn" data-tab="verification" aria-selected="false">Organizer Verification</button>
        </div>
    </section>

    <section class="content">
        <article id="panel-jwt" class="card tab-panel active">
            <span class="badge">JWT Session Token</span>
            <h2>Ringkasan Session</h2>
            <p class="sub">Token dibaca dari localStorage browser. Halaman ini juga menampilkan role user yang sedang login.</p>

            <div class="metric-row">
                <div class="metric">
                    <span>Status Token</span>
                    <strong id="tokenStatus">Mengecek...</strong>
                </div>

                <div class="metric">
                    <span>Role Aktif</span>
                    <strong id="userRole">-</strong>
                </div>
            </div>

            <div class="metric-row">
                <div class="metric">
                    <span>Panjang Token</span>
                    <strong id="tokenLength">0 karakter</strong>
                </div>

                <div class="metric">
                    <span>Akun Login</span>
                    <strong id="userName">-</strong>
                </div>
            </div>

            <div class="actions">
                <button id="refreshBtn" type="button" class="small-btn" onclick="refreshSession()">Refresh Session</button>
                <button id="logoutBtn" type="button" class="small-btn secondary" onclick="logoutUser()">Logout</button>
            </div>

            <div class="panel">
                <pre id="tokenPreview">Preview token akan tampil di sini.</pre>
            </div>

        </article>

        <article id="panel-verification" class="card tab-panel">
            <span class="badge">Organizer Verification</span>
            <h2>Verifikasi Organizer</h2>
            <p class="sub">Masukkan ID user organizer, lalu jalankan verifikasi. Request memakai token login yang tersimpan di browser.</p>

            <div class="field">
                <label for="userId">Organizer User ID</label>
                <input id="userId" type="number" min="1" value="1" placeholder="Masukkan user id">
            </div>

            <div class="actions">
                <button id="verifyBtn" type="button" onclick="verifyUser()">Verify Organizer</button>
            </div>

            <p id="status" class="status"></p>

            <div class="panel">
                <pre id="responsePanel">Belum ada response.</pre>
            </div>
        </article>

        <article id="panel-history" class="card tab-panel">
            <span class="badge">Donation History</span>
            <h2>Riwayat Donasi Pribadi</h2>
            <p class="sub">Data ini diambil dari API dan hanya menampilkan donasi milik user yang sedang login. Kamu bisa memfilter, mengurutkan, dan menghapus data milik sendiri.</p>

            <form id="historyForm">
                <div class="filters">
                    <div class="field">
                        <label for="campaignFilter">Campaign ID</label>
                        <input id="campaignFilter" type="number" min="1" placeholder="Opsional">
                    </div>

                    <div class="field">
                        <label for="minAmountFilter">Minimal Donasi</label>
                        <input id="minAmountFilter" type="number" min="0" placeholder="Opsional">
                    </div>

                    <div class="field">
                        <label for="maxAmountFilter">Maksimal Donasi</label>
                        <input id="maxAmountFilter" type="number" min="0" placeholder="Opsional">
                    </div>

                    <div class="field">
                        <label for="sortByFilter">Urutkan Berdasarkan</label>
                        <select id="sortByFilter">
                            <option value="created_at">Tanggal</option>
                            <option value="amount">Nominal</option>
                            <option value="campaign_id">Campaign ID</option>
                            <option value="id">ID Donasi</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="sortDirFilter">Arah Urutan</label>
                        <select id="sortDirFilter">
                            <option value="desc">Terbaru</option>
                            <option value="asc">Terlama</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="perPageFilter">Data per Halaman</label>
                        <select id="perPageFilter">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="20">20</option>
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button id="loadHistoryBtn" type="submit">Muat Riwayat</button>
                    <button id="resetHistoryBtn" type="button" class="small-btn secondary" onclick="resetDonationFilters()">Reset Filter</button>
                </div>
            </form>

            <p id="historyStatus" class="status"></p>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Campaign</th>
                        <th>Amount</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody id="historyTableBody">
                    <tr>
                        <td colspan="5" class="empty-state">Muat riwayat untuk melihat data donasi.</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="pager">
                <div class="pager-info" id="historyMeta">Belum ada data yang dimuat.</div>
                <div class="actions">
                    <button id="prevPageBtn" type="button" class="small-btn secondary" onclick="changeDonationPage(-1)">Prev</button>
                    <button id="nextPageBtn" type="button" class="small-btn secondary" onclick="changeDonationPage(1)">Next</button>
                </div>
            </div>
        </article>
    </section>
</main>

<script>
let donationState = {
    currentPage: 1,
    lastPage: 1,
    filters: {},
};

let donationLoaded = false;

function activateTab(tabName) {
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabPanels = document.querySelectorAll(".tab-panel");

    tabButtons.forEach((button) => {
        const isActive = button.dataset.tab === tabName;
        button.classList.toggle("active", isActive);
        button.setAttribute("aria-selected", String(isActive));
    });

    tabPanels.forEach((panel) => {
        const isActive = panel.id === `panel-${tabName}`;
        panel.classList.toggle("active", isActive);
    });

    if (tabName === "history" && !donationLoaded) {
        loadDonationHistory(1);
        donationLoaded = true;
    }
}

function renderTokenInfo() {
    const token = localStorage.getItem("token") || "";
    const userRoleValue = localStorage.getItem("user_role") || "belum tersimpan";
    const userNameValue = localStorage.getItem("user_name") || "-";
    const tokenStatus = document.getElementById("tokenStatus");
    const tokenLength = document.getElementById("tokenLength");
    const tokenPreview = document.getElementById("tokenPreview");
    const userRole = document.getElementById("userRole");
    const userName = document.getElementById("userName");

    if (!token) {
        tokenStatus.textContent = "Tidak tersedia";
        tokenLength.textContent = "0 karakter";
        tokenPreview.textContent = "Token belum ada. Login dulu untuk mendapatkan token.";
        userRole.textContent = userRoleValue;
        userName.textContent = userNameValue;
        return;
    }

    tokenStatus.textContent = "Tersedia";
    tokenLength.textContent = token.length + " karakter";
    tokenPreview.textContent = token.slice(0, 28) + "..." + token.slice(-18);
    userRole.textContent = userRoleValue;
    userName.textContent = userNameValue;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#39;");
}

function formatCurrency(value) {
    const numberValue = Number(value || 0);

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(numberValue);
}

function formatDate(value) {
    if (!value) {
        return "-";
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat("id-ID", {
        dateStyle: "medium",
        timeStyle: "short",
    }).format(date);
}

function buildDonationQuery(page = 1) {
    const campaignId = document.getElementById("campaignFilter").value.trim();
    const minAmount = document.getElementById("minAmountFilter").value.trim();
    const maxAmount = document.getElementById("maxAmountFilter").value.trim();
    const sortBy = document.getElementById("sortByFilter").value;
    const sortDir = document.getElementById("sortDirFilter").value;
    const perPage = document.getElementById("perPageFilter").value;

    const params = new URLSearchParams();

    params.set("page", String(page));
    params.set("sort_by", sortBy);
    params.set("sort_dir", sortDir);
    params.set("per_page", perPage);

    if (campaignId) {
        params.set("campaign_id", campaignId);
    }

    if (minAmount) {
        params.set("min_amount", minAmount);
    }

    if (maxAmount) {
        params.set("max_amount", maxAmount);
    }

    donationState.filters = Object.fromEntries(params.entries());

    return params.toString();
}

function renderDonationHistory(payload) {
    const tableBody = document.getElementById("historyTableBody");
    const historyMeta = document.getElementById("historyMeta");
    const prevPageBtn = document.getElementById("prevPageBtn");
    const nextPageBtn = document.getElementById("nextPageBtn");

    const items = Array.isArray(payload.data) ? payload.data : [];
    const meta = payload.meta || {};

    donationState.currentPage = meta.current_page || 1;
    donationState.lastPage = meta.last_page || 1;

    prevPageBtn.disabled = donationState.currentPage <= 1;
    nextPageBtn.disabled = donationState.currentPage >= donationState.lastPage;

    historyMeta.textContent = `Halaman ${donationState.currentPage} dari ${donationState.lastPage} • Total ${meta.total || items.length} data`;

    if (!items.length) {
        tableBody.innerHTML = '<tr><td colspan="5" class="empty-state">Tidak ada riwayat donasi yang cocok dengan filter saat ini.</td></tr>';
        return;
    }

    tableBody.innerHTML = items.map((item) => {
        const donationId = escapeHtml(item.id);
        const campaignId = escapeHtml(item.campaign_id);
        const amount = formatCurrency(item.amount);
        const createdAt = formatDate(item.created_at);

        return `
            <tr>
                <td>#${donationId}</td>
                <td>Campaign ${campaignId}</td>
                <td>${escapeHtml(amount)}</td>
                <td class="muted-cell">${escapeHtml(createdAt)}</td>
                <td>
                    <div class="row-actions">
                        <button type="button" class="small-btn danger" onclick="deleteDonation(${donationId})">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

async function loadDonationHistory(page = 1) {
    const token = localStorage.getItem("token");
    const historyStatus = document.getElementById("historyStatus");
    const loadHistoryBtn = document.getElementById("loadHistoryBtn");

    if (!token) {
        historyStatus.className = "status err";
        historyStatus.textContent = "Token belum tersedia. Login dulu untuk memuat riwayat donasi.";
        return;
    }

    loadHistoryBtn.disabled = true;
    historyStatus.className = "status";
    historyStatus.textContent = "Memuat riwayat donasi...";

    try {
        const query = buildDonationQuery(page);
        const res = await fetch(`/api/donations/history?${query}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        const raw = await res.text();
        let data = {};

        try {
            data = raw ? JSON.parse(raw) : {};
        } catch (jsonError) {
            data = { message: raw || "Respons server tidak terbaca." };
        }

        if (res.ok) {
            historyStatus.className = "status ok";
            historyStatus.textContent = data.message || "Riwayat donasi berhasil dimuat.";
            renderDonationHistory(data);
            return;
        }

        historyStatus.className = "status err";
        historyStatus.textContent = data.message || data.error || "Riwayat donasi gagal dimuat.";
        document.getElementById("historyTableBody").innerHTML = '<tr><td colspan="5" class="empty-state">Data belum bisa ditampilkan.</td></tr>';
    } catch (error) {
        historyStatus.className = "status err";
        historyStatus.textContent = "Gagal terhubung ke server.";
        document.getElementById("historyTableBody").innerHTML = '<tr><td colspan="5" class="empty-state">Terjadi masalah saat mengambil data.</td></tr>';
    } finally {
        loadHistoryBtn.disabled = false;
    }
}

function resetDonationFilters() {
    document.getElementById("campaignFilter").value = "";
    document.getElementById("minAmountFilter").value = "";
    document.getElementById("maxAmountFilter").value = "";
    document.getElementById("sortByFilter").value = "created_at";
    document.getElementById("sortDirFilter").value = "desc";
    document.getElementById("perPageFilter").value = "10";
    loadDonationHistory(1);
}

function changeDonationPage(direction) {
    const targetPage = donationState.currentPage + direction;

    if (targetPage < 1 || targetPage > donationState.lastPage) {
        return;
    }

    loadDonationHistory(targetPage);
}

async function verifyUser() {
    const token = localStorage.getItem("token");
    const status = document.getElementById("status");
    const responsePanel = document.getElementById("responsePanel");
    const verifyBtn = document.getElementById("verifyBtn");
    const userId = document.getElementById("userId").value;

    status.className = "status";

    if (!token) {
        status.className = "status err";
        status.textContent = "Token tidak ditemukan. Silakan login dulu.";
        responsePanel.textContent = "Tidak ada request yang dikirim karena token kosong.";
        return;
    }

    if (!userId || Number(userId) < 1) {
        status.className = "status err";
        status.textContent = "ID organizer tidak valid.";
        return;
    }

    verifyBtn.disabled = true;
    status.textContent = "Mengirim request verifikasi...";

    try {
        const res = await fetch('/api/verify/' + userId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });

        const raw = await res.text();
        let data = {};

        try {
            data = raw ? JSON.parse(raw) : {};
        } catch (jsonError) {
            data = { message: raw || 'Respons server tidak terbaca.' };
        }

        if (res.ok) {
            status.className = "status ok";
            status.textContent = data.message || "Verifikasi organizer berhasil.";
        } else {
            status.className = "status err";
            status.textContent = data.error || data.message || "Verifikasi gagal.";
        }

        responsePanel.textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        status.className = "status err";
        status.textContent = "Gagal terhubung ke server.";
        responsePanel.textContent = error.message;
    } finally {
        verifyBtn.disabled = false;
    }
}

function refreshSession() {
    renderTokenInfo();

    if (donationLoaded) {
        loadDonationHistory(donationState.currentPage || 1);
    }
}

function logoutUser() {
    localStorage.removeItem("token");
    localStorage.removeItem("user_role");
    localStorage.removeItem("user_name");
    window.location.href = "/login";
}

async function deleteDonation(id) {
    const token = localStorage.getItem("token");
    const historyStatus = document.getElementById("historyStatus");

    if (!token) {
        historyStatus.className = "status err";
        historyStatus.textContent = "Token belum tersedia. Login dulu untuk menghapus riwayat donasi.";
        return;
    }

    if (!window.confirm(`Hapus donasi #${id}?`)) {
        return;
    }

    try {
        const res = await fetch(`/api/donations/history/${id}`, {
            method: "DELETE",
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
        });

        const raw = await res.text();
        let data = {};

        try {
            data = raw ? JSON.parse(raw) : {};
        } catch (jsonError) {
            data = { message: raw || "Respons server tidak terbaca." };
        }

        if (res.ok) {
            historyStatus.className = "status ok";
            historyStatus.textContent = data.message || "Donasi berhasil dihapus.";
            loadDonationHistory(donationState.currentPage || 1);
            return;
        }

        historyStatus.className = "status err";
        historyStatus.textContent = data.message || data.error || "Donasi gagal dihapus.";
    } catch (error) {
        historyStatus.className = "status err";
        historyStatus.textContent = "Gagal terhubung ke server.";
    }
}

document.getElementById("historyForm").addEventListener("submit", function (event) {
    event.preventDefault();
    loadDonationHistory(1);
    donationLoaded = true;
});

document.querySelectorAll(".tab-btn").forEach((button) => {
    button.addEventListener("click", function () {
        activateTab(this.dataset.tab);
    });
});

renderTokenInfo();
activateTab("jwt");
</script>

</body>
</html>