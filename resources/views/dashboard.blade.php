<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Dashboard | Team 13</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #111827;
            --panel: rgba(15, 23, 42, 0.72);
            --panel-strong: rgba(15, 23, 42, 0.92);
            --border: rgba(148, 163, 184, 0.16);
            --text-main: #f8fafc;
            --text-soft: #cbd5e1;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --accent-2: #fbbf24;
            --success: #34d399;
            --danger: #fb7185;
            --warning: #f59e0b;
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
                radial-gradient(circle at 12% 12%, rgba(56, 189, 248, 0.18), transparent 26%),
                radial-gradient(circle at 85% 14%, rgba(251, 191, 36, 0.14), transparent 28%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
            overflow-x: hidden;
        }

        .grain {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.22;
            background-image: radial-gradient(rgba(255, 255, 255, 0.035) 0.7px, transparent 0.7px);
            background-size: 4px 4px;
        }

        .shell {
            display: grid;
            grid-template-columns: 272px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 24px 18px;
            border-right: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.55);
            backdrop-filter: blur(16px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 8px 18px;
            margin-bottom: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.9), rgba(251, 191, 36, 0.82));
            color: #08111f;
            font-weight: 800;
            font-family: "Space Grotesk", sans-serif;
            box-shadow: 0 16px 40px rgba(56, 189, 248, 0.24);
        }

        .brand-copy h1 {
            margin: 0;
            font-family: "Space Grotesk", sans-serif;
            font-size: 18px;
            letter-spacing: -0.02em;
        }

        .brand-copy p {
            margin: 4px 0 0;
            color: var(--text-muted);
            font-size: 12px;
        }

        .menu-title {
            margin: 18px 8px 10px;
            color: var(--text-muted);
            font-size: 11px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .menu {
            display: grid;
            gap: 8px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: var(--text-soft);
            text-decoration: none;
            border: 1px solid transparent;
            background: rgba(255, 255, 255, 0.03);
            transition: transform 160ms ease, border-color 160ms ease, background 160ms ease;
        }

        .menu-item:hover {
            transform: translateX(2px);
            border-color: rgba(56, 189, 248, 0.28);
            background: rgba(56, 189, 248, 0.08);
        }

        .menu-item.active {
            color: var(--text-main);
            border-color: rgba(56, 189, 248, 0.35);
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.16), rgba(251, 191, 36, 0.08));
        }

        .menu-item .label {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .menu-item .label strong {
            font-size: 14px;
            font-weight: 700;
        }

        .menu-item .label span {
            font-size: 12px;
            color: var(--text-muted);
        }

        .menu-item .status {
            font-size: 11px;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .menu-item.disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .sidebar-card {
            margin-top: 18px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.03));
        }

        .sidebar-card h3 {
            margin: 0 0 8px;
            font-family: "Space Grotesk", sans-serif;
        }

        .sidebar-card p {
            margin: 0;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.65;
        }

        .main {
            padding: 26px 26px 38px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.28);
            background: rgba(56, 189, 248, 0.12);
            color: #d9f5ff;
            padding: 8px 13px;
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        .hero {
            display: grid;
            gap: 16px;
            margin-bottom: 18px;
            padding: 20px 22px;
            border-radius: 24px;
            border: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0.03));
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.18);
        }

        .hero h2 {
            margin: 0;
            font-family: "Space Grotesk", sans-serif;
            font-size: clamp(32px, 4vw, 54px);
            line-height: 1.04;
            letter-spacing: -0.03em;
        }

        .hero p {
            margin: 0;
            max-width: 860px;
            color: var(--text-soft);
            font-size: 15px;
            line-height: 1.7;
        }

        .module-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .module-pill {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-soft);
            padding: 7px 11px;
            font-size: 12px;
        }

        .module-strip a {
            text-decoration: none;
            color: inherit;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .metric-card {
            padding: 18px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--panel);
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.16);
        }

        .metric-card .k {
            margin: 0 0 10px;
            color: var(--text-muted);
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        .metric-card .v {
            margin: 0;
            font-size: 28px;
            font-family: "Space Grotesk", sans-serif;
            letter-spacing: -0.03em;
        }

        .metric-card .s {
            margin-top: 10px;
            color: var(--text-soft);
            font-size: 12px;
            line-height: 1.5;
        }

        .workspace {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }

        .panel {
            border-radius: 24px;
            border: 1px solid var(--border);
            background: var(--panel);
            backdrop-filter: blur(14px);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.18);
        }

        .panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 20px 0;
        }

        .panel-header h3 {
            margin: 0;
            font-family: "Space Grotesk", sans-serif;
            font-size: 22px;
        }

        .panel-header p {
            margin: 6px 0 0;
            color: var(--text-soft);
            font-size: 14px;
            line-height: 1.6;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px 20px 0;
        }

        .filter-btn {
            appearance: none;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-soft);
            border-radius: 999px;
            padding: 9px 13px;
            cursor: pointer;
            transition: 160ms ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: rgba(56, 189, 248, 0.14);
            border-color: rgba(56, 189, 248, 0.34);
            color: var(--text-main);
        }

        .body {
            padding: 20px;
        }

        .form-grid {
            display: grid;
            gap: 12px;
        }

        .form-row {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr 1fr;
        }

        .field label {
            display: block;
            margin-bottom: 7px;
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            padding: 12px 13px;
            outline: none;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .field textarea {
            min-height: 112px;
            resize: vertical;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: rgba(56, 189, 248, 0.8);
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.16);
            transform: translateY(-1px);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            border: 0;
            border-radius: 14px;
            padding: 11px 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 160ms ease, opacity 160ms ease, box-shadow 160ms ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #04111e;
            background: linear-gradient(135deg, #7dd3fc, #fbbf24);
            box-shadow: 0 12px 24px rgba(56, 189, 248, 0.18);
        }

        .btn-secondary {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .btn-danger {
            color: #fff;
            background: rgba(251, 113, 133, 0.16);
            border: 1px solid rgba(251, 113, 133, 0.28);
        }

        .mini-note {
            margin-top: 14px;
            border-radius: 18px;
            border: 1px dashed rgba(148, 163, 184, 0.24);
            background: rgba(255, 255, 255, 0.03);
            padding: 14px;
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.65;
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 0 0 24px 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            text-align: left;
            padding: 14px 16px;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        tbody td {
            padding: 15px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            vertical-align: top;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .title-cell {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .desc-cell {
            color: var(--text-soft);
            font-size: 13px;
            line-height: 1.55;
            max-width: 460px;
        }

        .amount-cell {
            font-weight: 700;
            font-variant-numeric: tabular-nums;
        }

        .sub-amount {
            margin-top: 5px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .progress-wrap {
            display: grid;
            gap: 6px;
        }

        .progress-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.9), rgba(56, 189, 248, 0.9));
        }

        .progress-label {
            font-size: 12px;
            color: var(--text-soft);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .badge.active {
            background: rgba(52, 211, 153, 0.14);
            color: #8ef0c3;
            border: 1px solid rgba(52, 211, 153, 0.28);
        }

        .badge.done {
            background: rgba(251, 191, 36, 0.14);
            color: #ffe6a3;
            border: 1px solid rgba(251, 191, 36, 0.28);
        }

        .row-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .row-actions .btn {
            padding: 9px 12px;
            font-size: 12px;
        }

        .empty {
            padding: 30px 16px;
            color: var(--text-muted);
            text-align: center;
        }

        .toast {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 20;
            min-width: 280px;
            max-width: 360px;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(8, 15, 31, 0.88);
            color: var(--text-main);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.28);
            transform: translateY(16px);
            opacity: 0;
            pointer-events: none;
            transition: 180ms ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.ok {
            border-color: rgba(52, 211, 153, 0.3);
        }

        .toast.err {
            border-color: rgba(251, 113, 133, 0.32);
        }

        .muted {
            color: var(--text-muted);
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1200px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                height: auto;
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .workspace {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .main {
                padding: 18px 16px 34px;
            }

            .metrics {
                grid-template-columns: 1fr 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .pagination-info {
            color: var(--text-muted);
            font-size: 13px;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pagination-controls button {
            appearance: none;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-soft);
            border-radius: 10px;
            padding: 7px 13px;
            font-size: 13px;
            cursor: pointer;
            transition: 160ms ease;
        }

        .pagination-controls button:hover:not(:disabled) {
            background: rgba(56, 189, 248, 0.14);
            border-color: rgba(56, 189, 248, 0.34);
            color: var(--text-main);
        }

        .pagination-controls button:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .pagination-controls .page-current {
            color: var(--text-main);
            font-size: 13px;
            min-width: 80px;
            text-align: center;
        }

        .per-page-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .per-page-wrap select {
            appearance: none;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-soft);
            border-radius: 10px;
            padding: 6px 10px;
            font-size: 13px;
            cursor: pointer;
        }

        @media (max-width: 620px) {
            .metrics {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 18px;
            }

            .panel-header,
            .body {
                padding-left: 16px;
                padding-right: 16px;
            }

            thead {
                display: none;
            }

            tbody tr {
                display: grid;
                gap: 8px;
                padding: 14px 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            tbody td {
                display: block;
                padding: 0 16px;
                border-bottom: 0;
            }

            tbody td[data-label]::before {
                content: attr(data-label);
                display: block;
                margin-bottom: 4px;
                color: var(--text-muted);
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }
        }
    </style>
</head>
<body>
    <div class="grain"></div>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark">13</div>
                <div class="brand-copy">
                    <h1>Team 13 Hub</h1>
                    <p>Dashboard modul terintegrasi</p>
                </div>
            </div>

            <div class="menu-title">Navigasi</div>
            <nav class="menu">
                <a class="menu-item active" href="/">
                    <span class="label">
                        <strong>Dashboard Campaign</strong>
                        <span>Overview dan CRUD kampanye</span>
                    </span>
                    <span class="status">Aktif</span>
                </a>
                <a class="menu-item" href="/donation-processing">
                    <span class="label">
                        <strong>Donation Processing</strong>
                        <span>Input donasi dan total campaign</span>
                    </span>
                    <span class="status">Live</span>
                </a>
                <div class="menu-item disabled" aria-disabled="true">
                    <span class="label">
                        <strong>Modul Berikutnya</strong>
                        <span>Slot untuk modul tambahan tim</span>
                    </span>
                    <span class="status">Soon</span>
                </div>
            </nav>

            <div class="sidebar-card">
                <h3>Kenapa dashboard?</h3>
                <p>Struktur ini memudahkan modul baru ditambahkan tanpa mengubah alur utama. Setiap modul tinggal diberi menu dan panel sendiri.</p>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <div class="eyebrow">Campaign Management · Dashboard</div>
                <div class="muted">Root route: <span style="color:#e2e8f0">/</span> · Modul donation tetap tersedia di <span style="color:#e2e8f0">/donation-processing</span></div>
            </div>

            <section class="hero">
                <h2>Campaign Management</h2>
                <div class="module-strip">
                    <span class="module-pill">Campaign list</span>
                    <span class="module-pill">Create & edit campaign</span>
                    <span class="module-pill">Status aktif/selesai</span>
                    <a class="module-pill" href="/donation-categories">Manajemen kategori donasi</a>
                </div>
            </section>

            <section class="metrics">
                <article class="metric-card">
                    <p class="k">Total Campaign</p>
                    <p class="v" id="totalCampaigns">0</p>
                    <div class="s">Semua campaign yang terbaca dari API.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Campaign Aktif</p>
                    <p class="v" id="activeCampaigns">0</p>
                    <div class="s">Campaign yang masih berjalan.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Campaign Selesai</p>
                    <p class="v" id="finishedCampaigns">0</p>
                    <div class="s">Campaign yang sudah ditutup.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Total Target</p>
                    <p class="v" id="totalTarget">Rp0</p>
                    <div class="s">Akumulasi target donasi semua campaign.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Dana Terkumpul</p>
                    <p class="v" id="totalCollected">Rp0</p>
                    <div class="s">Akumulasi donasi masuk di semua campaign.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Donatur Aktif</p>
                    <p class="v" id="activeDonorsMetric">0</p>
                    <div class="s">Donor terverifikasi yang sudah pernah donasi sukses.</div>
                </article>
                <article class="metric-card">
                    <p class="k">Seed Donor Aktif</p>
                    <p class="v" id="seededActiveDonorsMetric">0</p>
                    <div class="s">Monitoring requirement minimal 20.000 donor aktif dari seeder.</div>
                </article>
            </section>

            <section class="workspace">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Campaign Management</h3>
                            <p>Kelola campaign: tambah, edit, hapus, ubah status.</p>
                        </div>
                    </div>

                    <div class="filters">
                        <button class="filter-btn active" type="button" data-filter="all">Semua</button>
                        <button class="filter-btn" type="button" data-filter="aktif">Aktif</button>
                        <button class="filter-btn" type="button" data-filter="selesai">Selesai</button>
                    </div>

                    <div class="body">
                        <form id="campaignForm" class="form-grid">
                            <input type="hidden" id="editingCampaignId" value="">
                            <div class="form-row">
                                <div class="field">
                                    <label for="title">Judul Campaign</label>
                                    <input type="text" id="title" required placeholder="Misal: Bantuan Pendidikan 2026">
                                </div>
                                <div class="field">
                                    <label for="targetAmount">Target Amount</label>
                                    <input type="number" id="targetAmount" min="0" step="0.01" required placeholder="50000000">
                                </div>
                            </div>
                            <div class="field">
                                <label for="description">Deskripsi</label>
                                <textarea id="description" placeholder="Tulis ringkasan campaign"></textarea>
                            </div>
                            <div class="form-row">
                                <div class="field">
                                    <label for="status">Status</label>
                                    <select id="status">
                                        <option value="aktif">Aktif</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>&nbsp;</label>
                                    <div class="actions">
                                        <button type="submit" class="btn btn-primary" id="submitButton">Simpan Campaign</button>
                                        <button type="button" class="btn btn-secondary" id="resetButton">Reset Form</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="mini-note">
                            Klik <strong>Edit</strong> pada tabel untuk memuat data ke form. Tombol <strong>Status</strong> akan menukar status campaign secara cepat.
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Target</th>
                                    <th>Terkumpul</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Diperbarui</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="campaignTableBody">
                                <tr>
                                    <td colspan="7" class="empty">Memuat campaign...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination" id="paginationBar">
                        <div class="pagination-info" id="paginationInfo">–</div>
                        <div class="pagination-controls">
                            <button type="button" id="btnPrevPage" disabled>← Prev</button>
                            <span class="page-current" id="pageCurrentLabel">Hal. 1</span>
                            <button type="button" id="btnNextPage" disabled>Next →</button>
                        </div>
                        <div class="per-page-wrap">
                            <label for="perPageSelect">Per halaman:</label>
                            <select id="perPageSelect">
                                <option value="15" selected>15</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        const campaignTableBody = document.getElementById("campaignTableBody");
        const totalCampaigns = document.getElementById("totalCampaigns");
        const activeCampaigns = document.getElementById("activeCampaigns");
        const finishedCampaigns = document.getElementById("finishedCampaigns");
        const totalTarget = document.getElementById("totalTarget");
        const totalCollected = document.getElementById("totalCollected");
        const activeDonorsMetric = document.getElementById("activeDonorsMetric");
        const seededActiveDonorsMetric = document.getElementById("seededActiveDonorsMetric");
        const filterButtons = Array.from(document.querySelectorAll("[data-filter]"));
        const campaignForm = document.getElementById("campaignForm");
        const editingCampaignId = document.getElementById("editingCampaignId");
        const titleInput = document.getElementById("title");
        const targetAmountInput = document.getElementById("targetAmount");
        const descriptionInput = document.getElementById("description");
        const statusInput = document.getElementById("status");
        const submitButton = document.getElementById("submitButton");
        const resetButton = document.getElementById("resetButton");
        const toast = document.getElementById("toast");

        // Pagination elements
        const paginationInfo     = document.getElementById("paginationInfo");
        const pageCurrentLabel   = document.getElementById("pageCurrentLabel");
        const btnPrevPage        = document.getElementById("btnPrevPage");
        const btnNextPage        = document.getElementById("btnNextPage");
        const perPageSelect      = document.getElementById("perPageSelect");

        let currentFilter = "all";
        let currentPage   = 1;
        let currentPerPage = 15;
        let lastMeta      = null; 
        let campaigns     = [];

        function escapeHtml(value) {
            return String(value)
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#039;");
        }

        function formatCurrency(value) {
            const numericValue = Number(value || 0);
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                maximumFractionDigits: 0,
            }).format(numericValue);
        }

        function formatNumber(value) {
            return new Intl.NumberFormat("id-ID", {
                maximumFractionDigits: 0,
            }).format(Number(value || 0));
        }

        function formatDate(value) {
            if (!value) {
                return "-";
            }

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) {
                return "-";
            }

            return new Intl.DateTimeFormat("id-ID", {
                dateStyle: "medium",
                timeStyle: "short",
            }).format(date);
        }

        function showToast(message, type = "ok") {
            toast.textContent = message;
            toast.className = "toast " + type + " show";
            window.clearTimeout(showToast.timer);
            showToast.timer = window.setTimeout(() => {
                toast.className = "toast";
            }, 2600);
        }

        function setFilterButtonState(filter) {
            filterButtons.forEach((button) => {
                button.classList.toggle("active", button.dataset.filter === filter);
            });
        }

        function updateMetrics(list) {
            const activeCount = list.filter((item) => item.status === "aktif").length;
            const finishedCount = list.filter((item) => item.status === "selesai").length;
            const totalTargetValue = list.reduce((sum, item) => sum + Number(item.target_amount || 0), 0);
            const totalCollectedValue = list.reduce((sum, item) => sum + Number(item.total_donations || 0), 0);

            totalCampaigns.textContent = lastMeta ? String(lastMeta.total) : String(list.length);
            activeCampaigns.textContent = String(activeCount);
            finishedCampaigns.textContent = String(finishedCount);
            totalTarget.textContent = formatCurrency(totalTargetValue);
            totalCollected.textContent = formatCurrency(totalCollectedValue);
        }

        function updatePaginationUI() {
            if (!lastMeta) return;

            const { current_page, last_page, from, to, total } = lastMeta;
            paginationInfo.textContent = `Menampilkan ${from ?? 0}–${to ?? 0} dari ${total} campaign`;
            pageCurrentLabel.textContent = `Hal. ${current_page} / ${last_page}`;
            btnPrevPage.disabled = current_page <= 1;
            btnNextPage.disabled = current_page >= last_page;
        }

        async function loadDonationStats() {
            try {
                const response = await fetch("/api/donations/stats", {
                    headers: { Accept: "application/json" },
                });

                if (!response.ok) throw new Error("Gagal memuat statistik donor.");

                const data = await response.json();
                activeDonorsMetric.textContent = formatNumber(data.active_donors || 0);
                seededActiveDonorsMetric.textContent = formatNumber(data.seeded_active_donors || 0);
            } catch {
                activeDonorsMetric.textContent = "-";
                seededActiveDonorsMetric.textContent = "-";
            }
        }

        function renderTable(list) {
            if (!list.length) {
                campaignTableBody.innerHTML = '<tr><td colspan="7" class="empty">Belum ada campaign untuk filter ini.</td></tr>';
                return;
            }

            campaignTableBody.innerHTML = list.map((campaign) => {
                const statusClass = campaign.status === "aktif" ? "active" : "done";
                const nextStatus = campaign.status === "aktif" ? "selesai" : "aktif";
                const toggleLabel = campaign.status === "aktif" ? "Tutup" : "Aktifkan";
                const target = Number(campaign.target_amount || 0);
                const collected = Number(campaign.total_donations || 0);
                const progress = target > 0 ? (collected / target) * 100 : 0;
                const progressWidth = Math.max(0, Math.min(progress, 100));
                const progressLabel = (Number.isFinite(progress) ? progress : 0).toFixed(1) + "%";

                return `
                    <tr>
                        <td data-label="Campaign">
                            <div class="title-cell">${escapeHtml(campaign.title ?? "-")}</div>
                            <div class="desc-cell">${escapeHtml(campaign.description ?? "Tidak ada deskripsi")}</div>
                        </td>
                        <td data-label="Target">${formatCurrency(campaign.target_amount)}</td>
                        <td data-label="Terkumpul">
                            <div class="amount-cell">${formatCurrency(collected)}</div>
                            <div class="sub-amount">Target ${formatCurrency(target)}</div>
                        </td>
                        <td data-label="Progress">
                            <div class="progress-wrap">
                                <div class="progress-track">
                                    <div class="progress-fill" style="width:${progressWidth}%"></div>
                                </div>
                                <div class="progress-label">${progressLabel}</div>
                            </div>
                        </td>
                        <td data-label="Status"><span class="badge ${statusClass}">${escapeHtml(campaign.status ?? "-")}</span></td>
                        <td data-label="Diperbarui">${formatDate(campaign.updated_at)}</td>
                        <td data-label="Aksi">
                            <div class="row-actions">
                                <button type="button" class="btn btn-secondary" data-action="edit" data-id="${campaign.id}">Edit</button>
                                <button type="button" class="btn btn-secondary" data-action="status" data-id="${campaign.id}" data-next-status="${nextStatus}">${toggleLabel}</button>
                                <button type="button" class="btn btn-danger" data-action="delete" data-id="${campaign.id}">Hapus</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join("");
        }

        function applyCampaignData(list, meta) {
            campaigns = list;
            lastMeta  = meta;
            updateMetrics(list);
            renderTable(list);
            updatePaginationUI();
        }

        async function loadCampaigns(filter = currentFilter, page = currentPage) {
            currentFilter = filter;
            currentPage   = page;
            setFilterButtonState(filter);

            campaignTableBody.innerHTML = '<tr><td colspan="7" class="empty">Memuat campaign...</td></tr>';

            const params = new URLSearchParams({
                page:     String(page),
                per_page: String(currentPerPage),
            });

            const endpoint = filter === "all"
                ? `/api/campaigns?${params}`
                : `/api/campaigns/status/${encodeURIComponent(filter)}?${params}`;

            try {
                const response = await fetch(endpoint, {
                    headers: { Accept: "application/json" },
                });

                if (!response.ok) throw new Error("Gagal memuat campaign.");

                const data = await response.json();

                const campaignList = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);
                const meta = data.current_page !== undefined ? {
                    current_page: data.current_page,
                    last_page:    data.last_page,
                    from:         data.from,
                    to:           data.to,
                    total:        data.total,
                } : null;

                applyCampaignData(campaignList, meta);
                await loadDonationStats();
            } catch (error) {
                console.error(error);
                campaignTableBody.innerHTML = '<tr><td colspan="7" class="empty">Data campaign tidak bisa dimuat.</td></tr>';
                activeDonorsMetric.textContent = "-";
                seededActiveDonorsMetric.textContent = "-";
                showToast("Gagal memuat campaign.", "err");
            }
        }

        function resetForm() {
            editingCampaignId.value = "";
            campaignForm.reset();
            statusInput.value = "aktif";
            submitButton.textContent = "Simpan Campaign";
        }

        function startEdit(campaign) {
            editingCampaignId.value = String(campaign.id);
            titleInput.value = campaign.title ?? "";
            targetAmountInput.value = campaign.target_amount ?? 0;
            descriptionInput.value = campaign.description ?? "";
            statusInput.value = campaign.status ?? "aktif";
            submitButton.textContent = "Update Campaign";
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        async function saveCampaign(event) {
            event.preventDefault();

            const payload = {
                title: titleInput.value.trim(),
                description: descriptionInput.value.trim(),
                target_amount: Number(targetAmountInput.value || 0),
                status: statusInput.value,
            };

            if (!payload.title) {
                showToast("Judul campaign wajib diisi.", "err");
                return;
            }

            const campaignId = editingCampaignId.value;
            const endpoint = campaignId ? "/api/campaigns/" + campaignId : "/api/campaigns";
            const method = campaignId ? "PUT" : "POST";

            try {
                const response = await fetch(endpoint, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                if (!response.ok) {
                    const errorBody = await response.json().catch(() => ({}));
                    const message = errorBody.message ?? "Gagal menyimpan campaign.";
                    throw new Error(message);
                }

                showToast(campaignId ? "Campaign berhasil diperbarui." : "Campaign berhasil ditambahkan.", "ok");
                resetForm();
                await loadCampaigns(currentFilter);
            } catch (error) {
                console.error(error);
                showToast(error.message || "Gagal menyimpan campaign.", "err");
            }
        }

        async function toggleCampaignStatus(id, nextStatus) {
            try {
                const response = await fetch("/api/campaigns/" + id + "/status", {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ status: nextStatus }),
                });

                if (!response.ok) {
                    throw new Error("Gagal mengubah status campaign.");
                }

                showToast("Status campaign diperbarui.", "ok");
                await loadCampaigns(currentFilter);
            } catch (error) {
                console.error(error);
                showToast(error.message || "Gagal mengubah status campaign.", "err");
            }
        }

        async function deleteCampaign(id) {
            const confirmed = window.confirm("Hapus campaign ini? Aksi ini tidak bisa dibatalkan.");
            if (!confirmed) {
                return;
            }

            try {
                const response = await fetch("/api/campaigns/" + id, {
                    method: "DELETE",
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error("Gagal menghapus campaign.");
                }

                if (editingCampaignId.value === String(id)) {
                    resetForm();
                }

                showToast("Campaign berhasil dihapus.", "ok");
                await loadCampaigns(currentFilter);
            } catch (error) {
                console.error(error);
                showToast(error.message || "Gagal menghapus campaign.", "err");
            }
        }

        campaignTableBody.addEventListener("click", async (event) => {
            const button = event.target.closest("button[data-action]");
            if (!button) return;

            const id = button.dataset.id;
            const campaign = campaigns.find((item) => String(item.id) === String(id));
            const action = button.dataset.action;

            if (action === "edit" && campaign) {
                startEdit(campaign);
                return;
            }

            if (action === "status") {
                await toggleCampaignStatus(id, button.dataset.nextStatus || "aktif");
                return;
            }

            if (action === "delete") {
                await deleteCampaign(id);
            }
        });

        filterButtons.forEach((button) => {
            button.addEventListener("click", () => loadCampaigns(button.dataset.filter || "all", 1));
        });

        btnPrevPage.addEventListener("click", () => {
            if (currentPage > 1) loadCampaigns(currentFilter, currentPage - 1);
        });

        btnNextPage.addEventListener("click", () => {
            if (lastMeta && currentPage < lastMeta.last_page) loadCampaigns(currentFilter, currentPage + 1);
        });

        perPageSelect.addEventListener("change", () => {
            currentPerPage = Number(perPageSelect.value);
            loadCampaigns(currentFilter, 1); 
        });

        campaignForm.addEventListener("submit", saveCampaign);
        resetButton.addEventListener("click", resetForm);

        loadCampaigns();
    </script>
</body>
</html>