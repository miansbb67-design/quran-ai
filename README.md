# Bayan AI Quran Teacher & Learning Platform

![Bayan AI Quran Teacher](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

A modern, responsive Islamic web application built with **Laravel 12**, **Bootstrap 5.3**, and **Web Speech AI**, empowering students and seekers of knowledge to read the Holy Quran, listen to authentic recitations, practice their recitation with real-time phonetic voice feedback, and systematically memorize the sacred text using cognitive spaced repetition.

---

## 🌟 Key Features

### 1. 📖 Interactive Quran Reader
- Complete catalog of all **114 Surahs** formatted in Medina Uthmani script.
- Verse-by-verse recitation audio streaming from renowned Qaris (**Sheikh Mishary Rashid Alafasy**, **Abdul Basit**, **Mahmoud Khalil Al-Husary**, **Abu Bakr Al-Shatri**).
- Reliable English translations (Muhammad Asad, Saheeh International, Yusuf Ali, Pickthall).
- Continuous Surah playback mode with automated verse scrolling.
- Instant verse bookmarking with personal reflection notes.
- Dynamic font sizing controls (A+ / A- / Reset) for accessibility.

### 2. 🎙️ AI Voice Recitation Coach
- Integrated speech recognition studio powered by the **Web Speech API** (`ar-SA`).
- **Phonetic Normalization Engine** comparing uttered Arabic against the Uthmani script.
- **Word-by-word alignment visualizer**:
  - 🟩 **Green**: Correctly pronounced and sequenced.
  - 🟨 **Amber**: Partial match / uncertain phonetics.
  - 🟥 **Red**: Missing, substituted, or mispronounced words.
- Real-time confidence score percentage meter and tajweed advice.
- Manual Arabic text input fallback for devices or browsers without microphone permissions.
- Modular AI provider architecture (`RecitationAnalysisInterface`) ready for Whisper / Azure / Gemini plug-ins.

### 3. 🧠 Hifz Spaced Repetition Gym
- Comprehensive memorization journey tracker: *Memorized*, *Practicing*, *Currently Learning*.
- Interactive **Active Recall Gym** with multiple learning modes:
  - **Show All**: Standard read-through.
  - **Mask Words**: Words are masked in grey; clicking any individual word reveals it.
  - **Blind Recall**: Entire verse is blurred; students recite from heart and tap to verify.
- Self-evaluation grading (*Hard / Repeat*, *Good / Practicing*, *Memorized!*).
- Spaced-repetition scheduling algorithm automatically spacing revisions at 1, 3, 7, 14, and 30-day intervals.

### 4. 📊 Student Dashboard & Analytics
- Daily reading streak counter with gamified milestones.
- Daily goal completion progress bar (Ayahs read vs. target).
- Chart.js interactive visualizations for 30-day practice trends, scores, and retention status.
- GDPR-compliant data sovereignty: students can purge their practice history or reset their account anytime.

### 5. 🛡️ Role-Based Admin Control Panel
- Distinct role permissions: **Student (`user`)**, **Teacher (`admin`)**, and **Platform Owner (`owner`)**.
- User & Student Directory: Search, filter by role/status, inspect learning history, promote roles, suspend/activate accounts.
- Content Management System:
  - Dynamic **FAQs** accordion editor.
  - Institutional **Static Pages** editor (About, Privacy Policy, Terms).
  - Urgent **Announcements** broadcaster with banner ticker.
- System Settings & AI Configuration:
  - Branding, default reciters, default translations.
  - AI confidence score threshold and provider driver selection.
  - Cache clearing and system flush utilities.
  - Real-time API telemetry logs and admin audit trails.

---

## 🛠️ Technology Stack

| Layer | Technology |
|---|---|
| **Backend** | Laravel 12 (PHP 8.2+) |
| **Authentication** | Laravel Breeze with custom Bootstrap 5.3 Blade templates |
| **Database** | MySQL (XAMPP compatible) |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), jQuery 3.7.1 |
| **UI Framework** | Bootstrap 5.3.3 & Bootstrap Icons 1.11.3 |
| **Typography** | Google Fonts *Amiri*, *Noto Naskh Arabic*, *Plus Jakarta Sans* |
| **Data Source** | AlQuran Cloud API (Cached in database/Redis with fallback error logging) |
| **Charts** | Chart.js 4.4 |
| **Testing** | PHPUnit / Pest with 32 automated tests |

---

## 🚀 Quick Start & Installation

### Prerequisites
- PHP 8.2 or higher with `pdo_mysql`, `curl`, `mbstring`, `openssl` extensions enabled.
- MySQL database server (e.g. XAMPP, Laragon, or standalone MySQL).
- Composer.

### 1. Database Setup
Create a new MySQL database:
```sql
CREATE DATABASE quran_ai_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Environment Configuration
Ensure your `.env` contains the database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quran_ai_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations & Seeders
Execute the migrations and seed the database with demo users, settings, and sample data:
```bash
php artisan migrate
php artisan db:seed
```

### 4. Serve the Application
```bash
php artisan serve
```
Open your browser and visit: **`http://127.0.0.1:8000`**

---

## 🔑 Demo Login Accounts

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Student** | `student@quran-ai.com` | `Password123!` | Dashboard, Quran Reader, AI Studio, Hifz Gym |
| **Administrator** | `admin@quran-ai.com` | `Password123!` | All Student Features + Admin Console, FAQs, Users |
| **Platform Owner** | `owner@quran-ai.com` | `Password123!` | Full Authority, Role Assignment, System Settings |

---

## 🧪 Running Automated Tests

Run the full automated test suite verifying auth, RBAC, Quran API, AI recitation analysis, and user data:
```bash
php artisan test
```
*Current test status: 32 tests, 78 assertions, 100% passing.*

---

## 📁 Key File Map

- **Routes**: [`routes/web.php`](file:///c:/xampp/htdocs/quran-ai/routes/web.php)
- **Design System CSS**: [`public/css/quran-app.css`](file:///c:/xampp/htdocs/quran-ai/public/css/quran-app.css)
- **Quran API Service**: [`app/Services/Quran/QuranApiService.php`](file:///c:/xampp/htdocs/quran-ai/app/Services/Quran/QuranApiService.php)
- **AI Recitation Engine**: [`app/Services/AI/QuranRecitationAnalysisService.php`](file:///c:/xampp/htdocs/quran-ai/app/Services/AI/QuranRecitationAnalysisService.php)
- **Customer Controllers**: [`app/Http/Controllers/Customer/`](file:///c:/xampp/htdocs/quran-ai/app/Http/Controllers/Customer/)
- **Admin Controllers**: [`app/Http/Controllers/Admin/`](file:///c:/xampp/htdocs/quran-ai/app/Http/Controllers/Admin/)
- **Blade Views**: [`resources/views/`](file:///c:/xampp/htdocs/quran-ai/resources/views/)
- **Feature Tests**: [`tests/Feature/QuranAiPlatformTest.php`](file:///c:/xampp/htdocs/quran-ai/tests/Feature/QuranAiPlatformTest.php)

---

## 📜 License
This software is crafted with love and devotion for the global Muslim community. Open-source under the MIT license.
