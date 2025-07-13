# FolioFlow

**FolioFlow** is a lightweight PHP application for tracking cryptocurrency investments. It allows you to register, log in, add or close positions and monitor profit/loss using real‑time price data.

## Features

- User registration and authentication
- Add, update and close investments
- Real‑time price fetching from Binance
- Portfolio analytics and ROI calculation
- Profit/loss visualization
- Investment history and logs
- Middleware based access protection

## Requirements

- PHP 7.4 or higher with the `PDO` and `CURL` extensions
- MySQL 5.7 or higher
- A web server such as Apache or Nginx

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/EmirhanSergen/FolioFlow.git
   cd FolioFlow
   ```
2. **Create a database**
   ```sql
   CREATE DATABASE folioflow;
   ```
3. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` with your credentials:
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
4. **Configure your web server**
   Point your server root to this directory. For quick testing you can use PHP's built‑in server:
   ```bash
   php -S localhost:8000
   ```

## Usage

1. Visit `/register` to create a new account and log in.
2. Use the dashboard to add investments and track your portfolio.
3. View analytics for ROI and closed positions.

## Project Structure

```text
FolioFlow/
├── api/            # (reserved for future APIs)
├── classes/        # Core classes such as Database and Investment
├── config/         # Configuration and .env loader
├── controllers/    # Application controllers
├── middleware/     # Authentication and price checking
├── views/          # HTML templates
├── logs/           # Generated log files (ignored in Git)
├── index.php       # Application entry point
├── router.php      # Route definitions
└── .env            # Environment configuration (not committed)
```


## License

This project is available under the [MIT License](LICENSE).
