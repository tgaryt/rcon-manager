# Source RCON Interface

A modern, professional PHP-based RCON interface for Source engine game servers. Built with OOP architecture and Tailwind CSS for a clean, responsive experience.

## Features

- **Multi-server RCON management** - Send commands to multiple servers simultaneously
- **Server management** - Add, remove, and manage server configurations
- **Command filtering** - Allow/block specific commands for security
- **Cooldown system** - Prevent command spam with configurable delays
- **Modern UI** - Clean, responsive design with Tailwind CSS
- **Real-time feedback** - Live server status and command responses

## Installation

### 1. Clone Repository
```bash
git clone https://github.com/tgaryt/rcon-manager
cd rcon-manager
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Database Setup
- Create a MySQL database named `src_rcon`
- Import the SQL schema:
```bash
mysql -u username -p src_rcon < sql/schema.sql
```

### 4. Environment Configuration
```bash
cp .env.example .env
```
Edit `.env` with your database credentials and RCON settings.

### 5. Web Server Setup
Point your web server document root to the `public/` directory.

### 6. Set Permissions
```bash
sudo chown -R www-data:www-data /path/to/project
sudo chmod -R 755 /path/to/project
sudo chmod -R 775 /path/to/project/config
```

## Usage

1. Access the admin panel to add your Source servers
2. Configure RCON passwords for each server
3. Use the main interface to send commands to multiple servers
4. Monitor responses and manage your server infrastructure
