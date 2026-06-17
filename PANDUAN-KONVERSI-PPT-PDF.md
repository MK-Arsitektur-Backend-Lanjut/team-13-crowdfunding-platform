# PANDUAN KONVERSI KE POWERPOINT & PDF

## File Laporan
📄 **File:** `LAPORAN-FINAL-DONATION-PROCESSING.md`

---

## Metode 1: Gunakan Pandoc (RECOMMENDED) ⭐

### Step 1: Install Pandoc
```bash
# Windows (via Chocolatey)
choco install pandoc

# macOS (via Homebrew)
brew install pandoc

# Linux
sudo apt-get install pandoc
```

### Step 2: Convert Markdown to PowerPoint
```bash
cd C:\Users\Fajar Alfandi\Documents\GitHub\team-13-crowdfunding-platform

# Convert to PPTX
pandoc LAPORAN-FINAL-DONATION-PROCESSING.md -o LAPORAN-FINAL-DONATION-PROCESSING.pptx
```

### Step 3: Open & Edit di PowerPoint
1. Open file `LAPORAN-FINAL-DONATION-PROCESSING.pptx`
2. Edit formatting, add colors, images
3. Customize template sesuai kebutuhan

### Step 4: Export ke PDF
1. File → Export As → Export as PDF
2. Atau: File → Save As → Format: PDF

---

## Metode 2: Copy-Paste ke PowerPoint

### Step 1: Buat Presentation Baru
1. Open Microsoft PowerPoint
2. Create blank presentation

### Step 2: Buat Slide Berdasarkan Outline
Untuk setiap slide dalam markdown:

```
## SLIDE 1: JUDUL
↓
Buat slide baru di PowerPoint dengan konten sesuai
```

### Step 3: Formatting
1. Gunakan tema PowerPoint yang menarik
2. Add colors & design elements
3. Insert images dari `postman/` folder jika perlu

### Step 4: Save as PDF
1. File → Export As → Export as PDF

---

## Metode 3: Gunakan Google Slides (ONLINE)

### Step 1: Buka Google Slides
https://docs.google.com/presentation/

### Step 2: Create New Presentation

### Step 3: Copy Content
1. Buka `LAPORAN-FINAL-DONATION-PROCESSING.md`
2. Copy text per slide
3. Paste di Google Slides

### Step 4: Download as PDF
1. File → Download → PDF Document

---

## Metode 4: Gunakan LibreOffice Impress

### Step 1: Install LibreOffice
```bash
# Download dari https://www.libreoffice.org/
```

### Step 2: Open & Convert
```bash
libreoffice --impress LAPORAN-FINAL-DONATION-PROCESSING.md
```

### Step 3: Save as PPTX
File → Save As → Format: ODP/PPTX

### Step 4: Export to PDF
File → Export as PDF

---

## RECOMMENDED WORKFLOW ✅

**Untuk hasil terbaik, gunakan:**

```
LAPORAN-FINAL-DONATION-PROCESSING.md
        ↓
  Pandoc atau Copy-Paste
        ↓
LAPORAN-FINAL-DONATION-PROCESSING.pptx (edit di PowerPoint)
        ↓
  Add Design & Colors
        ↓
LAPORAN-FINAL-DONATION-PROCESSING.pdf (Export)
```

---

## Tips Formatting PowerPoint

### Slide Layout
- **Judul Slide:** Gunakan Title Slide layout
- **Content Slides:** Gunakan Title and Content layout
- **Section Headers:** Gunakan Section Header layout

### Color Scheme (Suggested)
- **Primary:** Blue (#0066CC) - Professional
- **Secondary:** Green (#00CC66) - Success/Checkmarks
- **Accent:** Gray (#333333) - Text/Details

### Font (Suggested)
- **Title:** Calibri Bold, 44pt
- **Subtitle:** Calibri, 28pt
- **Body:** Calibri, 18-20pt
- **Code/Technical:** Courier New, 14pt

### Visual Elements to Add
- ✅ Checkmarks untuk completed items
- 📊 Chart untuk metrics
- 🔗 Links untuk GitHub repo
- 📱 Screenshots dari aplikasi (jika ada)
- 🎯 Icons untuk sections

---

## File Structure dalam Laporan

### Content Breakdown:
- **Slide 1:** Title Page
- **Slide 2:** Table of Contents  
- **Slides 3-6:** Team Rules & Features Overview
- **Slides 7-9:** Feature Details
- **Slides 10-17:** Strategy & Tools
- **Slides 18-22:** Results & Deliverables
- **Slides 23-25:** Testing & Verification
- **Slides 26-29:** Lessons & Conclusion
- **Slide 30:** Thank You / Q&A

**Total Slides:** ~30

---

## CHECKLIST SEBELUM SUBMIT

- [ ] File sudah dalam format PowerPoint (.pptx)
- [ ] File sudah di-export ke PDF (.pdf)
- [ ] Nama file sesuai: `LAPORAN-FINAL-DONATION-PROCESSING.pdf`
- [ ] Semua slide sudah terisi dengan konten
- [ ] Formatting sudah rapi dan profesional
- [ ] Links & references sudah aktif
- [ ] File sudah di-save di folder yang benar
- [ ] Deadline: Sabtu, 25 April 2026 pukul 17:00 ✓

---

## QUICK COMMAND (Windows PowerShell)

```powershell
# Install Pandoc
choco install pandoc -y

# Navigate to project folder
cd "C:\Users\Fajar Alfandi\Documents\GitHub\team-13-crowdfunding-platform"

# Convert to PPTX
pandoc LAPORAN-FINAL-DONATION-PROCESSING.md -o LAPORAN-FINAL-DONATION-PROCESSING.pptx

# Buka file di PowerPoint untuk editing
.\LAPORAN-FINAL-DONATION-PROCESSING.pptx
```

---

## Support Resources

- **Pandoc Documentation:** https://pandoc.org/
- **PowerPoint Help:** https://support.microsoft.com/office/
- **Google Slides Help:** https://support.google.com/docs/
- **LibreOffice Help:** https://www.libreoffice.org/get-help/

---

**PENTING:** Deadline adalah **Sabtu, 25 April 2026 Pukul 17:00**

Pastikan file sudah di-submit tepat waktu! ⏰
