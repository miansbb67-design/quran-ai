# Bayan AI Quran Teacher

A modern, production-ready Quran learning platform built with native PHP 8.2+ (PDO), MySQL, Bootstrap 5.3, GSAP, and Speech AI.

## Features

- **The Holy Quran:** All 114 Surahs and 30 Juz with authentic Uthmani text, English/Urdu translations, and verse-by-verse audio synchronization.
- **Authentic Qaris:** Crystal-clear Murattal recitations by Sheikh Mishary Rashid Alafasy, AbdulBaset AbdulSamad, Mahmoud Khalil Al-Husary, and Abdur-Rahman As-Sudais.
- **AI Quran Recitation Teacher:** Microphone-based recitation analysis with word-by-word difference detection, accuracy scoring, and educational feedback.
- **Hifz & Revision Tracker:** Schedule daily memorization targets, track revision cycles, and maintain consistency.
- **Student Dashboard:** Track last-read Surah & Ayah, bookmarks, and AI recitation practice logs.
- **Executive Admin Panel (`/admin`):**
  - Real-time platform statistics & Chart.js traffic analytics.
  - User management (Student / Admin roles, account status toggles).
  - SEO & Search Console center with page-level meta management and live `robots.txt` editor.
- **Security & Integrity:**
  - PDO prepared statements against SQL injection.
  - Strict CSRF protection on state-changing requests.
  - Role-based server-side access control.
  - Canonical Quranic Arabic preservation.

## Setup Instructions

1. **Database Setup:**
   Import `database/migrations/schema.sql` and `database/seeds/init.sql` into MySQL database `quran_ai`.
2. **Seed Default Accounts:**
   Run `php database/seeds/SeedUsers.php`
   - Admin: `admin@bayan.test` / `Admin@123456`
   - Student: `student@bayan.test` / `Student@123456`
3. **Environment:**
   Configure your database and AI keys in `.env`.
