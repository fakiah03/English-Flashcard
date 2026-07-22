# 🧠 English Memory Flashcard

A modern, intelligent English vocabulary learning web application built with **Native PHP**, **MySQL**, and **Bootstrap 5**. This system helps users identify and track words they repeatedly forget using a smart duplicate detection algorithm and spaced repetition scheduling.

---
[![AI Generated](https://img.shields.io/badge/AI_Generated-Project-blueviolet.svg)](https://github.com/fakiah03)

---

## 🌟 Features

### 🔑 Authentication
- User Registration & Login
- Simulated Forgot Password
- Session-based Authentication
- Admin & User Roles

### 📖 Vocabulary Management
- Add new words with **meaning**, **example sentence**, **pronunciation (IPA)**, and **category**
- **Auto Generate** meaning & pronunciation using the free [Dictionary API](https://dictionaryapi.dev/) (no API key needed)
- Search, Edit, and Delete vocabulary
- Favorite and Archive words

### 🚨 Smart Duplicate Detection
> This is NOT a normal flashcard system.

When a user tries to add a word that already exists, the system will:
- ✅ **NOT** create a duplicate record
- 🔢 Increase the **Forgot Count** by 1
- 🕐 Update the **Last Forgotten Date**
- 📋 Store the event in **Vocabulary History**
- 💬 Show a SweetAlert2 popup notification:

  > *"You already added this word before. You have forgotten this word 5 times. Last forgotten: 2 days ago."*

### 🃏 Flashcard Mode
- Show English word → Click to reveal meaning, example, pronunciation
- Rate with: **Easy**, **Good**, **Hard**, **Again**
- Updates **Spaced Repetition** schedule automatically

### 🗓️ Spaced Repetition
- Review levels: **1 → 3 → 7 → 14 → 30 → 60 days**
- Auto-generates today's review list
- Mastered status when maximum level is reached

### 🎮 Quiz Mode
- Random **Multiple Choice** questions
- Score tracking saved to history
- Skip question option

### 📊 Statistics & Dashboard
- Total Vocabulary, Due Today, Mastered Words
- Forgotten Today counter
- **Learning Streak** tracker (updates only on actual review/quiz activity)
- Chart.js visualizations:
  - Most Forgotten Words (Bar Chart)
  - Mastered vs Learning (Doughnut Chart)

### 🛡️ Admin Panel
- Overview dashboard with platform-wide statistics
- View and manage all registered users
- Total vocabulary, forgotten count, quizzes taken

---

## 🛠️ Tech Stack

| Layer        | Technology              |
|--------------|-------------------------|
| Backend      | PHP 8.x (Native/No Framework) |
| Database     | MySQL via PDO           |
| Frontend     | HTML5, CSS3, JavaScript |
| UI Framework | Bootstrap 5.3           |
| Icons        | Bootstrap Icons         |
| Charts       | Chart.js                |
| Alerts       | SweetAlert2             |
| Dictionary   | [Free Dictionary API](https://dictionaryapi.dev/) |
| Local Server | Laragon                 |

---

## 📁 Project Structure

```
english_flashcard/
├── config/
│   ├── database.php        # PDO connection config
│   └── setup.php           # Auto database & table creation
├── includes/
│   ├── auth_check.php      # Session protection
│   ├── functions.php       # Helper functions (streak, spaced repetition)
│   ├── header.php          # HTML head + CSS links
│   ├── footer.php          # JS scripts
│   └── sidebar.php         # Navigation sidebar
├── assets/
│   ├── css/
│   │   └── style.css       # Custom styles (pastel purple & dark mode)
│   └── js/
│       └── main.js         # Theme toggle, Dictionary API calls
├── auth/
│   ├── login.php
│   ├── register.php
│   ├── forgot_password.php
│   └── logout.php
├── user/
│   ├── dashboard.php       # Main dashboard with stats
│   ├── add_word.php        # Add new vocabulary
│   ├── vocabulary.php      # List, search, delete vocabulary
│   ├── flashcards.php      # Flashcard review mode
│   ├── process_review.php  # Handles flashcard difficulty rating
│   ├── quiz.php            # Quiz mode
│   └── statistics.php      # Charts & learning stats
├── admin/
│   ├── dashboard.php       # Admin overview
│   └── users.php           # Manage all users
├── uploads/                # Profile photo uploads
└── index.php               # Entry point (redirects to login/dashboard)
```

---

## ⚙️ Installation & Setup

### Prerequisites
- [Laragon](https://laragon.org/) (with MySQL on port **3307**)
- PHP 8.x
- A modern web browser

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/YOUR_USERNAME/english-memory-flashcard.git
   ```

2. **Link to Laragon's www folder** (recommended via symlink)
   ```powershell
   # Run as Administrator in PowerShell
   New-Item -ItemType SymbolicLink -Path "C:\laragon\www\english_flashcard" -Target "C:\path\to\english-memory-flashcard"
   ```

3. **Start Laragon** — Make sure Apache and MySQL (port 3307) are running.

4. **Run the setup script** to auto-create the database and all tables:
   ```
   http://localhost/english_flashcard/config/setup.php
   ```

5. **Open the app:**
   ```
   http://localhost/english_flashcard/
   ```

6. **Register** a new account and start learning!

### Database Configuration

The database connection is configured in `config/database.php`:

```php
$host     = 'localhost';
$port     = '3307';
$dbname   = 'english_flashcard';
$username = 'root';
$password = '';
```

> Modify these values if your Laragon setup uses different credentials.

---

## 🗄️ Database Tables

| Table               | Description                              |
|---------------------|------------------------------------------|
| `users`             | User accounts and roles                  |
| `vocabulary`        | All vocabulary entries with spaced rep data |
| `vocabulary_history`| Log of forgotten word events             |
| `favorites`         | User's favorited words                   |
| `quiz_history`      | Quiz results and scores                  |
| `learning_streak`   | Daily streak tracking per user           |
| `notifications`     | In-app notifications                     |

---

## 🔐 Security

- ✅ PDO Prepared Statements (prevents SQL Injection)
- ✅ `htmlspecialchars()` for all output (prevents XSS)
- ✅ `password_hash()` / `password_verify()` for passwords
- ✅ Session-based Authentication
- ✅ Role-based Access Control (user / admin)

---

## 🎨 UI & Design

- 🎨 **Theme:** Pastel Purple & Dark Purple
- 🌙 **Dark Mode** toggle (saved via cookie)
- 💎 **Glassmorphism** card design
- ✨ **Smooth animations** and hover effects
- 📱 **Fully Responsive** with Bootstrap 5 grid

---

## 👤 Author

Made with ❤️ for English learners who want to stop forgetting the same words over and over!
