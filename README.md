# 📊 FolioFlow — Investment Portfolio Tracker

**FolioFlow** is a lightweight PHP-based web application to track cryptocurrency investments, manage portfolios, and monitor profit/loss with real-time price updates.

---

## 🚀 Features

- ✅ User Registration & Login (Authentication)
- ✅ Add / Update / Close Investments
- ✅ Real-time Cryptocurrency Price Fetching (via Binance API)
- ✅ Portfolio Analytics & ROI Calculation
- ✅ Profit/Loss Visualization
- ✅ Investment History & Logs
- ✅ Middleware-based Access Protection

---

## ⚙️ Requirements

- PHP **7.4+**
- MySQL **5.7+**
- Apache or Nginx
- `PDO` and `CURL` extensions enabled
- Composer (optional, for future improvements)

---

## 📦 Installation Guide

1. **Clone the repository**
   ```bash
   git clone https://github.com/EmirhanSergen/FolioFlow.git
   cd FolioFlow
   ```

2. **Create your MySQL database**
   ```sql
   CREATE DATABASE folioflow;
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   Edit the `.env` file with your own database credentials:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=folioflow
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   LOGIN_URL=/FolioFlow/login
   DASHBOARD_URL=/FolioFlow/dashboard
   APP_ENV=development
   ```

4. **Point your web server’s root to the project directory**
   - Apache: configure `DocumentRoot` to point to `/path/to/FolioFlow`
   - Nginx: use `root /path/to/FolioFlow;`

---

## 📁 Project Structure

```
FolioFlow/
├── api/                   # (Reserved for future REST APIs)
├── classes/               # Core classes (Database, Investment, Analytics, etc.)
├── config/                # Configuration files (.env loader, DB config)
├── controllers/           # Page controllers (dashboard, login, investments, etc.)
├── middleware/            # Authentication and price check middleware
├── views/                 # HTML views and partials
├── logs/                  # Error logs (auto-generated)
├── index.php              # App entry point & router
├── router.php             # Route definitions
└── .env                   # Environment configuration (ignored by Git)
```

---

## 🧪 Development Tips

- Use `dd($value)` helper to debug variables (disabled in production).
- Use `urlIs('/path')` to highlight active links in navigation.
- Custom middleware ensures prices are updated only every 15 minutes for efficiency.

---

## 📄 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

---
