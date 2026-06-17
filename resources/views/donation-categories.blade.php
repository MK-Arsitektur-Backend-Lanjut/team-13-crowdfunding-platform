<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori Donasi | Team 13</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #0f172a;
            --bg-2: #111827;
            --panel: rgba(15, 23, 42, 0.72);
            --border: rgba(148, 163, 184, 0.18);
            --text-main: #f8fafc;
            --text-soft: #cbd5e1;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --danger: #fb7185;
            --ok: #34d399;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Plus Jakarta Sans", sans-serif;
            color: var(--text-main);
            background:
                radial-gradient(circle at 10% 10%, rgba(56, 189, 248, 0.16), transparent 28%),
                radial-gradient(circle at 85% 20%, rgba(251, 191, 36, 0.12), transparent 30%),
                linear-gradient(135deg, var(--bg-1), var(--bg-2));
        }

        .page {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px 16px 36px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(56, 189, 248, 0.3);
            background: rgba(56, 189, 248, 0.12);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
        }

        .links {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .links a {
            color: var(--text-main);
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 999px;
            padding: 7px 12px;
            font-size: 12px;
        }

        h1 {
            margin: 0 0 8px;
            font-family: "Space Grotesk", sans-serif;
            font-size: clamp(30px, 5vw, 48px);
            letter-spacing: -0.03em;
        }

        .lead {
            margin: 0 0 18px;
            color: var(--text-soft);
            max-width: 760px;
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 14px;
        }

        .card {
            border: 1px solid var(--border);
            background: var(--panel);
            border-radius: 20px;
            padding: 16px;
            backdrop-filter: blur(12px);
        }

        .card h2 {
            margin: 0 0 8px;
            font-size: 22px;
            font-family: "Space Grotesk", sans-serif;
        }

        .muted { color: var(--text-soft); font-size: 14px; margin: 0 0 12px; }

        .field { margin-bottom: 10px; }
        .field label { display: block; font-size: 13px; margin-bottom: 6px; }

        .field input, .field textarea {
            width: 100%;
            padding: 11px 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            outline: none;
        }

        .field textarea { min-height: 95px; resize: vertical; }

        .actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }

        .btn {
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary { background: linear-gradient(135deg, #7dd3fc, #fbbf24); color: #071322; }
        .btn-secondary { background: rgba(255, 255, 255, 0.08); color: var(--text-main); border: 1px solid rgba(255, 255, 255, 0.14); }
        .btn-danger { background: rgba(251, 113, 133, 0.18); color: #fff; border: 1px solid rgba(251, 113, 133, 0.32); }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); text-align: left; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.08em; }
        .name { font-weight: 700; }
        .desc { font-size: 13px; color: var(--text-soft); margin-top: 4px; max-width: 360px; line-height: 1.5; }
        .row-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty { text-align: center; color: var(--text-muted); padding: 24px 10px; }

        .toast {
            position: fixed;
            right: 16px;
            bottom: 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(8, 15, 31, 0.9);
            color: var(--text-main);
            padding: 12px 14px;
            min-width: 220px;
            opacity: 0;
            transform: translateY(8px);
            transition: 160ms ease;
            pointer-events: none;
        }

        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.ok { border-color: rgba(52, 211, 153, 0.35); }
        .toast.err { border-color: rgba(251, 113, 133, 0.35); }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="top">
            <div class="badge">Modul Kategori Donasi</div>
            <div class="links">
                <a href="/">Dashboard Campaign</a>
                <a href="/donation-processing">Donation Processing</a>
            </div>
        </div>

        <h1>Manajemen Kategori Donasi</h1>
        <p class="lead">
            Kelola master kategori donasi untuk standardisasi data. Halaman ini terhubung langsung ke endpoint
            <code>/api/donation-categories</code>.
        </p>

        <section class="grid">
            <article class="card">
                <h2>Form Kategori</h2>
                <p class="muted">Tambah kategori baru atau edit kategori yang sudah ada.</p>
                <form id="categoryForm">
                    <input type="hidden" id="editingCategoryId" value="">
                    <div class="field">
                        <label for="name">Nama Kategori</label>
                        <input id="name" type="text" placeholder="Contoh: Pendidikan" required>
                    </div>
                    <div class="field">
                        <label for="description">Deskripsi</label>
                        <textarea id="description" placeholder="Deskripsi singkat kategori"></textarea>
                    </div>
                    <div class="actions">
                        <button id="submitButton" class="btn btn-primary" type="submit">Simpan Kategori</button>
                        <button id="resetButton" class="btn btn-secondary" type="button">Reset Form</button>
                    </div>
                </form>
            </article>

            <article class="card">
                <h2>Daftar Kategori</h2>
                <p class="muted">Klik edit untuk memuat data ke form.</p>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Diperbarui</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <tr><td colspan="3" class="empty">Memuat kategori...</td></tr>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </main>

    <div id="toast" class="toast"></div>

    <script>
        const categoryTableBody = document.getElementById("categoryTableBody");
        const categoryForm = document.getElementById("categoryForm");
        const editingCategoryId = document.getElementById("editingCategoryId");
        const nameInput = document.getElementById("name");
        const descriptionInput = document.getElementById("description");
        const submitButton = document.getElementById("submitButton");
        const resetButton = document.getElementById("resetButton");
        const toast = document.getElementById("toast");

        let categories = [];

        function escapeHtml(value) {
            return String(value ?? "")
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#039;");
        }

        function formatDate(value) {
            if (!value) return "-";
            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return "-";

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
            }, 2400);
        }

        function resetForm() {
            editingCategoryId.value = "";
            categoryForm.reset();
            submitButton.textContent = "Simpan Kategori";
        }

        function renderTable(list) {
            if (!list.length) {
                categoryTableBody.innerHTML = '<tr><td colspan="3" class="empty">Belum ada kategori donasi.</td></tr>';
                return;
            }

            categoryTableBody.innerHTML = list.map((category) => {
                return `
                    <tr>
                        <td>
                            <div class="name">${escapeHtml(category.name || "-")}</div>
                            <div class="desc">${escapeHtml(category.description || "Tanpa deskripsi")}</div>
                        </td>
                        <td>${formatDate(category.updated_at)}</td>
                        <td>
                            <div class="row-actions">
                                <button class="btn btn-secondary" type="button" data-action="edit" data-id="${category.id}">Edit</button>
                                <button class="btn btn-danger" type="button" data-action="delete" data-id="${category.id}">Hapus</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join("");
        }

        async function loadCategories() {
            try {
                const response = await fetch("/api/donation-categories", {
                    headers: { Accept: "application/json" },
                });
                if (!response.ok) {
                    throw new Error("Gagal memuat kategori.");
                }

                const data = await response.json();
                categories = Array.isArray(data) ? data : [];
                renderTable(categories);
            } catch (error) {
                console.error(error);
                categoryTableBody.innerHTML = '<tr><td colspan="3" class="empty">Data kategori tidak bisa dimuat.</td></tr>';
                showToast(error.message || "Gagal memuat kategori.", "err");
            }
        }

        function startEdit(category) {
            editingCategoryId.value = String(category.id);
            nameInput.value = category.name || "";
            descriptionInput.value = category.description || "";
            submitButton.textContent = "Update Kategori";
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        async function saveCategory(event) {
            event.preventDefault();

            const payload = {
                name: nameInput.value.trim(),
                description: descriptionInput.value.trim(),
            };

            if (!payload.name) {
                showToast("Nama kategori wajib diisi.", "err");
                return;
            }

            const categoryId = editingCategoryId.value;
            const endpoint = categoryId ? "/api/donation-categories/" + categoryId : "/api/donation-categories";
            const method = categoryId ? "PUT" : "POST";

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
                    throw new Error(errorBody.message || "Gagal menyimpan kategori.");
                }

                showToast(categoryId ? "Kategori diperbarui." : "Kategori ditambahkan.", "ok");
                resetForm();
                await loadCategories();
            } catch (error) {
                console.error(error);
                showToast(error.message || "Gagal menyimpan kategori.", "err");
            }
        }

        async function deleteCategory(id) {
            if (!window.confirm("Hapus kategori ini?")) return;

            try {
                const response = await fetch("/api/donation-categories/" + id, {
                    method: "DELETE",
                    headers: { Accept: "application/json" },
                });

                if (!response.ok) {
                    const errorBody = await response.json().catch(() => ({}));
                    throw new Error(errorBody.message || "Gagal menghapus kategori.");
                }

                if (editingCategoryId.value === String(id)) {
                    resetForm();
                }

                showToast("Kategori dihapus.", "ok");
                await loadCategories();
            } catch (error) {
                console.error(error);
                showToast(error.message || "Gagal menghapus kategori.", "err");
            }
        }

        categoryTableBody.addEventListener("click", async (event) => {
            const button = event.target.closest("button[data-action]");
            if (!button) return;

            const id = button.dataset.id;
            const action = button.dataset.action;
            const category = categories.find((item) => String(item.id) === String(id));

            if (action === "edit" && category) {
                startEdit(category);
                return;
            }

            if (action === "delete") {
                await deleteCategory(id);
            }
        });

        categoryForm.addEventListener("submit", saveCategory);
        resetButton.addEventListener("click", resetForm);

        loadCategories();
    </script>
</body>
</html>
