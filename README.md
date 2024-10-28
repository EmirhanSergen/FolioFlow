# FolioFlow - Investment Portfolio Tracker

A PHP-based web application for tracking cryptocurrency investments and managing investment portfolios.

## Features

- User Authentication (Register/Login)
- Investment Portfolio Management
- Real-time Cryptocurrency Price Updates
- Position Tracking
- Profit/Loss Calculations
- Investment History

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- CURL extension enabled
- PDO extension enabled

## Installation

1. Clone the repository:
```bash
git clone https://github.com/your-username/FolioFlow.git
```

2. Create a MySQL database and import the database schema:
```sql
CREATE DATABASE folioflow;
```

3. Copy `.env.example` to `.env` and update the configuration:
```bash
cp .env.example .env
```

4. Update the database configuration in `.env`:
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=folioflow
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Configure your web server to point to the project's root directory

## Project Structure

```
FolioFlow/
├── api/
├── classes/
├── config/
├── controllers/
├── middleware/
├── views/
└── index.php
```

## License

MIT License

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request
