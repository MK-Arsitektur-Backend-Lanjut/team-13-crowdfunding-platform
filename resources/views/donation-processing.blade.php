<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Processing | Team 13</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0d1b2a;
            --bg-2: #1b263b;
            --card: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.14);
            --text-main: #f4f7fb;
            --text-soft: #c7d3e4;
            --accent: #7bdff2;
            --accent-2: #ffd166;
            --danger: #ff5d73;
            --success: #2dd4bf;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--text-main);
            background: radial-gradient(circle at 10% 5%, #20304d 0%, transparent 40%),
                        radial-gradient(circle at 90% 20%, #243a5b 0%, transparent 35%),
                        linear-gradient(135deg, var(--bg-1), var(--bg-2));
            overflow-x: hidden;
        }

        .noise {
            position: fixed;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.03) 0.6px, transparent 0.6px);
            background-size: 4px 4px;
            pointer-events: none;
            opacity: 0.35;
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 28px 18px 64px;
        }

        .hero {
            display: grid;
            gap: 18px;
            margin-bottom: 24px;
            animation: rise 700ms ease-out;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            background: rgba(123, 223, 242, 0.16);
            border: 1px solid rgba(123, 223, 242, 0.35);
            border-radius: 999px;
            padding: 8px 14px;
            color: #dff8ff;
            font-size: 13px;
            letter-spacing: 0.4px;
        }

        h1 {
            margin: 0;
            font-family: "Space Grotesk", sans-serif;
            font-size: clamp(32px, 6vw, 54px);
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .hero p {
            margin: 0;
            max-width: 760px;
            color: var(--text-soft);
            font-size: clamp(15px, 2.4vw, 18px);
        }

        .module-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .module-pill {
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.09);
            padding: 6px 10px;
            font-size: 12px;
            letter-spacing: 0.3px;
            color: #e6f0fd;
        }

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: 1.25fr 0.95fr;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            backdrop-filter: blur(10px);
            padding: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
        }

        .panel h2 {
            margin: 0 0 6px;
            font-family: "Space Grotesk", sans-serif;
            letter-spacing: -0.01em;
        }

        .panel-sub {
            margin: 0 0 16px;
            color: var(--text-soft);
            font-size: 14px;
        }

        .fields {
            display: grid;
            gap: 12px;
        }

        .two {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr 1fr;
        }

        .field label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: #deebf9;
            font-weight: 600;
            letter-spacing: 0.25px;
        }

        .field input,
        .field textarea {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: var(--text-main);
            padding: 12px 13px;
            font-size: 15px;
            outline: none;
            transition: border-color 180ms, box-shadow 180ms, transform 180ms;
        }

        .field textarea {
            min-height: 86px;
            resize: vertical;
        }

        .field input:focus,
        .field textarea:focus {
            border-color: rgba(123, 223, 242, 0.85);
            box-shadow: 0 0 0 4px rgba(123, 223, 242, 0.18);
            transform: translateY(-1px);
        }

        .switch-wrap {
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .switch-wrap .text {
            font-size: 14px;
            color: var(--text-soft);
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.22);
            transition: 180ms;
            cursor: pointer;
        }

        .slider:before {
            content: "";
            position: absolute;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            left: 4px;
            top: 4px;
            background: #fff;
            transition: 180ms;
        }

        .switch input:checked + .slider {
            background: rgba(45, 212, 191, 0.55);
        }

        .switch input:checked + .slider:before {
            transform: translateX(22px);
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        button {
            border: 0;
            border-radius: 12px;
            padding: 11px 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 140ms ease, box-shadow 160ms ease, opacity 160ms ease;
        }

        button:active {
            transform: translateY(1px);
        }

        .btn-primary {
            color: #092032;
            background: linear-gradient(135deg, var(--accent), #8cffcc);
            box-shadow: 0 8px 20px rgba(123, 223, 242, 0.34);
        }

        .btn-secondary {
            color: #f2f8ff;
            background: linear-gradient(135deg, #415a77, #4f7197);
        }

        .stats {
            display: grid;
            gap: 12px;
            margin-bottom: 12px;
        }

        .stat {
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            padding: 12px;
        }

        .stat .label {
            font-size: 12px;
            letter-spacing: 0.45px;
            text-transform: uppercase;
            color: var(--text-soft);
        }

        .stat .value {
            margin-top: 6px;
            font-size: 24px;
            font-weight: 800;
            color: #f9fdff;
            font-variant-numeric: tabular-nums;
        }

        .feed {
            margin-top: 14px;
            border: 1px dashed rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px;
            min-height: 140px;
            display: grid;
            align-content: start;
            gap: 8px;
            background: rgba(0, 0, 0, 0.12);
        }

        .feed-item {
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.13);
            padding: 8px 10px;
            color: #e8f3ff;
            font-size: 13px;
        }

        .mini {
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-soft);
        }

        .campaign-insight {
            margin-top: 14px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 12px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
        }

        .campaign-insight h3 {
            margin: 0 0 8px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.45px;
            color: var(--text-soft);
        }

        .campaign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .campaign-metric {
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 9px;
            background: rgba(0, 0, 0, 0.12);
        }

        .campaign-metric .k {
            font-size: 11px;
            color: var(--text-soft);
            letter-spacing: 0.35px;
            text-transform: uppercase;
        }

        .campaign-metric .v {
            margin-top: 3px;
            font-size: 20px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .toast {
            position: fixed;
            right: 16px;
            bottom: 16px;
            min-width: 220px;
            max-width: 380px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px 13px;
            font-size: 13px;
            line-height: 1.45;
            transform: translateY(130%);
            transition: transform 240ms ease;
            z-index: 40;
        }

        .toast.show {
            transform: translateY(0);
        }

        .toast.ok {
            background: rgba(45, 212, 191, 0.2);
            color: #d4fff6;
            border-color: rgba(45, 212, 191, 0.45);
        }

        .toast.err {
            background: rgba(255, 93, 115, 0.2);
            color: #ffe0e5;
            border-color: rgba(255, 93, 115, 0.45);
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

        @media (max-width: 930px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page {
                padding: 18px 12px 48px;
            }

            .panel {
                padding: 16px;
                border-radius: 14px;
            }

            .two {
                grid-template-columns: 1fr;
            }

            .actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="noise"></div>
    <div class="page">
        <section class="hero">
            <span class="badge">STUDI KASUS TIM 13 · Donation Processing</span>
            <h1>Kelola Donasi Viral Tanpa Hitung Ganda</h1>
            <p>
                Form ini terhubung ke API Donation Processing: input donasi, dukung mode anonim,
                dan cek akumulasi total campaign secara langsung.
            </p>
            <div class="module-strip">
                <span class="module-pill">Campaign Management API</span>
                <span class="module-pill">Donation Processing API</span>
                <span class="module-pill">Integration Branch Preview</span>
            </div>
        </section>

        <section class="grid">
            <article class="panel">
                <h2>Input Donasi</h2>
                <p class="panel-sub">Setiap submit mengirim idempotency key otomatis agar request dobel tidak tercatat dua kali.</p>

                <form id="donationForm" class="fields">
                    <div class="two">
                        <div class="field">
                            <label for="campaignId">Campaign ID</label>
                            <input type="number" id="campaignId" name="campaign_id" min="1" value="100" required>
                        </div>
                        <div class="field">
                            <label for="amount">Nominal Donasi (Rupiah)</label>
                            <input type="number" id="amount" name="amount" min="1" value="50000" required>
                        </div>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label for="donorName">Nama Donatur</label>
                            <input type="text" id="donorName" name="donor_name" maxlength="255" placeholder="Contoh: Fajar">
                        </div>
                        <div class="field">
                            <label for="userId">User ID (opsional)</label>
                            <input type="number" id="userId" name="user_id" min="1" placeholder="Kosongkan jika guest">
                        </div>
                    </div>

                    <div class="field">
                        <label for="note">Pesan Donasi (opsional)</label>
                        <textarea id="note" name="note" maxlength="1000" placeholder="Semoga bermanfaat untuk yang membutuhkan"></textarea>
                    </div>

                    <div class="switch-wrap">
                        <span class="text">Aktifkan donasi anonim</span>
                        <label class="switch">
                            <input type="checkbox" id="isAnonymous" name="is_anonymous">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn-primary" id="submitBtn">Proses Donasi</button>
                        <button type="button" class="btn-secondary" id="refreshBtn">Refresh Total Campaign</button>
                    </div>
                </form>

                <p class="mini" id="idemKeyLabel"></p>
            </article>

            <aside class="panel">
                <h2>Campaign Live Total</h2>
                <p class="panel-sub">Pantau total dana terkumpul dari endpoint agregasi.</p>

                <div class="stats">
                    <div class="stat">
                        <div class="label">Campaign Aktif</div>
                        <div class="value" id="campaignValue">-</div>
                    </div>
                    <div class="stat">
                        <div class="label">Total Donasi</div>
                        <div class="value" id="totalValue">Rp 0</div>
                    </div>
                </div>

                <div class="feed" id="feed">
                    <div class="feed-item">Belum ada aktivitas. Kirim donasi pertama sekarang.</div>
                </div>

                <div class="campaign-insight">
                    <h3>Campaign Snapshot</h3>
                    <div class="campaign-grid">
                        <div class="campaign-metric">
                            <div class="k">Total Campaign</div>
                            <div class="v" id="campaignCountAll">0</div>
                        </div>
                        <div class="campaign-metric">
                            <div class="k">Campaign Aktif</div>
                            <div class="v" id="campaignCountActive">0</div>
                        </div>
                    </div>
                </div>
            </aside>
        </section>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        const donationForm = document.getElementById("donationForm");
        const campaignIdInput = document.getElementById("campaignId");
        const amountInput = document.getElementById("amount");
        const donorNameInput = document.getElementById("donorName");
        const userIdInput = document.getElementById("userId");
        const noteInput = document.getElementById("note");
        const isAnonymousInput = document.getElementById("isAnonymous");
        const refreshBtn = document.getElementById("refreshBtn");
        const submitBtn = document.getElementById("submitBtn");
        const campaignValue = document.getElementById("campaignValue");
        const totalValue = document.getElementById("totalValue");
        const feed = document.getElementById("feed");
        const campaignCountAll = document.getElementById("campaignCountAll");
        const campaignCountActive = document.getElementById("campaignCountActive");
        const toast = document.getElementById("toast");
        const idemKeyLabel = document.getElementById("idemKeyLabel");

        function rupiah(value) {
            return new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                maximumFractionDigits: 0,
            }).format(value || 0);
        }

        function createIdempotencyKey() {
            if (window.crypto && window.crypto.randomUUID) {
                return window.crypto.randomUUID();
            }

            return "manual-" + Date.now() + "-" + Math.random().toString(16).slice(2);
        }

        let currentIdemKey = createIdempotencyKey();
        idemKeyLabel.textContent = "Idempotency key: " + currentIdemKey;

        function showToast(message, type = "ok") {
            toast.className = "toast " + (type === "err" ? "err" : "ok");
            toast.textContent = message;
            requestAnimationFrame(() => toast.classList.add("show"));

            setTimeout(() => {
                toast.classList.remove("show");
            }, 2400);
        }

        function pushFeed(text) {
            const item = document.createElement("div");
            item.className = "feed-item";
            item.textContent = text;
            feed.prepend(item);

            while (feed.children.length > 7) {
                feed.removeChild(feed.lastChild);
            }
        }

        async function refreshCampaignTotal() {
            const campaignId = Number(campaignIdInput.value || 0);

            if (!campaignId) {
                showToast("Campaign ID belum valid.", "err");
                return;
            }

            try {
                const response = await fetch("/api/campaigns/" + campaignId + "/donations/total");
                if (!response.ok) {
                    throw new Error("Gagal mengambil total campaign.");
                }

                const data = await response.json();
                campaignValue.textContent = String(data.campaign_id ?? campaignId);
                totalValue.textContent = rupiah(Number(data.total_donations || 0));
                pushFeed("Total campaign " + campaignId + " diperbarui ke " + totalValue.textContent + ".");
            } catch (error) {
                showToast(error.message || "Terjadi kesalahan saat refresh total.", "err");
            }
        }

        async function refreshCampaignSnapshot() {
            try {
                const [allResponse, activeResponse] = await Promise.all([
                    fetch("/api/campaigns"),
                    fetch("/api/campaigns/status/aktif"),
                ]);

                if (!allResponse.ok || !activeResponse.ok) {
                    throw new Error("Gagal mengambil snapshot campaign.");
                }

                const allData = await allResponse.json();
                const activeData = await activeResponse.json();

                campaignCountAll.textContent = String(Array.isArray(allData) ? allData.length : 0);
                campaignCountActive.textContent = String(Array.isArray(activeData) ? activeData.length : 0);
            } catch (error) {
                campaignCountAll.textContent = "-";
                campaignCountActive.textContent = "-";
            }
        }

        donationForm.addEventListener("submit", async (event) => {
            event.preventDefault();

            const campaignId = Number(campaignIdInput.value || 0);
            const amount = Number(amountInput.value || 0);
            const isAnonymous = isAnonymousInput.checked;
            const donorName = donorNameInput.value.trim();
            const userId = userIdInput.value.trim();
            const note = noteInput.value.trim();

            if (!campaignId || !amount) {
                showToast("Campaign ID dan nominal wajib diisi.", "err");
                return;
            }

            const payload = {
                campaign_id: campaignId,
                amount: amount,
                is_anonymous: isAnonymous,
            };

            if (donorName !== "") payload.donor_name = donorName;
            if (userId !== "") payload.user_id = Number(userId);
            if (note !== "") payload.note = note;

            submitBtn.disabled = true;
            submitBtn.style.opacity = "0.7";

            try {
                const response = await fetch("/api/donations", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Idempotency-Key": currentIdemKey,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok) {
                    const message = data.message || "Gagal memproses donasi.";
                    throw new Error(message);
                }

                const donorLabel = data.data?.is_anonymous ? "Anonymous" : (data.data?.donor_name || "Donatur");
                showToast("Donasi berhasil diproses.");
                pushFeed(
                    donorLabel +
                    " berdonasi " +
                    rupiah(Number(data.data?.amount || amount)) +
                    " untuk campaign " +
                    String(data.data?.campaign_id || campaignId) +
                    "."
                );

                await refreshCampaignTotal();
                await refreshCampaignSnapshot();

                currentIdemKey = createIdempotencyKey();
                idemKeyLabel.textContent = "Idempotency key: " + currentIdemKey;
            } catch (error) {
                showToast(error.message || "Terjadi kesalahan saat submit.", "err");
            } finally {
                submitBtn.disabled = false;
                submitBtn.style.opacity = "1";
            }
        });

        refreshBtn.addEventListener("click", refreshCampaignTotal);

        isAnonymousInput.addEventListener("change", () => {
            if (isAnonymousInput.checked) {
                donorNameInput.value = "";
                donorNameInput.disabled = true;
                donorNameInput.placeholder = "Nama disembunyikan";
            } else {
                donorNameInput.disabled = false;
                donorNameInput.placeholder = "Contoh: Fajar";
            }
        });

        refreshCampaignTotal();
        refreshCampaignSnapshot();
    </script>
</body>
</html>
