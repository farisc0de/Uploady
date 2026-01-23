<div align="center">
<a href="https://ibb.co/LXPzsfRs"><img src="https://raw.githubusercontent.com/farisc0de/Uploady/refs/heads/main/uploady_logo.png" alt="Uploady Logo" border="0" /></a>
</div>

![](https://img.shields.io/github/license/farisc0de/Uploady) ![](https://img.shields.io/github/v/release/farisc0de/Uploady) ![](https://img.shields.io/github/repo-size/farisc0de/Uploady) ![](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) [![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=farisc0de_Uploady&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=farisc0de_Uploady)

# Uploady

Uploady is a modern, secure file uploader script with multi-file upload support.

It comes with more than 20 features and you can set it up in less than 5 minutes.

Built with PHP 8.x and MySQL, featuring a modern Bootstrap 5.3 UI.

Free and open source under the MIT license.

## Features

- **Multi-File Upload** - Drag and drop support with progress tracking
- **5 Protection Levels**
  - MIME type validation
  - Extension whitelist
  - File size limits (role-based)
  - Forbidden filename blocking
  - ClamAV virus scanning (optional)
- **Admin Panel** - Charts, stats, and complete file management
- **Role-based Access** - User roles with configurable upload size limits
- **Data Collection** - IP address, browser, OS, and country detection
- **Localization** - Multi-language support with RTL compatibility
- **Customization** - Custom CSS/JS, logo, favicon, AdSense, and Google Analytics
- **File Management** - Auto-delete after X days or X downloads
- **Pages System** - Create and manage custom pages
- **Report Abuse** - Built-in abuse reporting system
- **Social Sharing** - Share uploaded files on social media
- **API Access** - RESTful API with API key authentication

## Project Structure

```
uploady/
├── actions/           # AJAX action handlers (upload, delete, etc.)
├── admin/             # Admin panel pages and settings
├── api/               # REST API endpoints
├── assets/            # CSS, JS, and static assets
├── components/        # Reusable PHP components
├── config/            # Configuration files
├── languages/         # Language translation files
├── logic/             # Business logic handlers
├── modules/           # Feature modules
├── profile/           # User profile pages
├── src/Uploady/       # Core application classes
│   ├── Handler/       # Upload and file handlers
│   ├── Auth.php       # Authentication
│   ├── Database.php   # Database wrapper
│   ├── Mailer.php     # Email functionality
│   ├── Role.php       # Role management
│   ├── Settings.php   # Application settings
│   ├── User.php       # User management
│   └── Utils.php      # Utility functions
├── storage/           # File storage directory
├── uploads/           # User uploaded files
└── vendor/            # Composer dependencies
```

## Screenshots

![](https://i.imgur.com/jALS2DN.png)

![](https://i.imgur.com/fTe1FCZ.png)

## Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Required PHP extensions: `pdo`, `pdo_mysql`, `gd`, `fileinfo`, `json`, `mbstring`
- Optional: ClamAV for virus scanning

## Installation

### Standard Installation

1. Upload all files to your server
2. Run `composer install` in the `uploady/` directory
3. Modify `config/config.php` with your database credentials:

```php
define("DB_HOST", "localhost");
define("DB_USER", "your_username");
define("DB_PASS", "your_password");
define("DB_NAME", "uploady");
define("SITE_URL", "https://yourdomain.com/");
```

4. Set directory permissions:

```bash
chmod 755 -R uploady/
chmod 775 uploady/uploads/
chmod 775 uploady/storage/
```

5. Navigate to `install.php` in your browser
6. Follow the installation wizard
7. Delete `install.php` after successful installation

### Docker Deployment

1. Clone the repository:

```bash
git clone https://github.com/farisc0de/Uploady
cd Uploady/
```

2. Update `config/config.php` for Docker:

```php
define("DB_HOST", "db");
define("DB_USER", "uploady");
define("DB_PASS", "uploady");
define("DB_NAME", "uploady");
```

3. Build and run:

```bash
docker-compose up -d
```

## ClamAV Integration (Optional)

Uploady supports ClamAV for real-time virus scanning of uploaded files.

### Installing ClamAV

**Ubuntu/Debian:**

```bash
sudo apt update
sudo apt install clamav clamav-daemon
sudo systemctl start clamav-daemon
sudo systemctl enable clamav-daemon
```

**CentOS/RHEL:**

```bash
sudo yum install epel-release
sudo yum install clamav clamav-update clamd
sudo systemctl start clamd@scan
sudo systemctl enable clamd@scan
```

**macOS (Homebrew):**

```bash
brew install clamav
cp /usr/local/etc/clamav/freshclam.conf.sample /usr/local/etc/clamav/freshclam.conf
cp /usr/local/etc/clamav/clamd.conf.sample /usr/local/etc/clamav/clamd.conf
freshclam
clamd
```

### Update Virus Definitions

```bash
sudo freshclam
```

### Configure Uploady for ClamAV

In your admin panel, enable the "Virus Scanner" option.

### Verify ClamAV is Running

```bash
# Check socket exists
ls -la /var/run/clamav/clamd.sock

# Test ClamAV
echo "X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*" | clamdscan -
```

## API Usage

### Upload a File

```bash
curl --location 'https://yourdomain.com/api/upload' \
--header 'X-API-KEY: your_api_key_here' \
--form 'file=@"/path/to/your/file.jpg"'
```

### Response

```json
{
  "filename": "original_name.jpg",
  "filehash": "hashed_filename.jpg",
  "filesize": "1.5 MB",
  "uploaddate": "2025-01-23 12:00:00",
  "fileurl": "https://yourdomain.com/uploads/user_id/hashed_filename.jpg",
  "filetype": "image/jpeg",
  "file_id": "abc123...",
  "user_id": "xyz789..."
}
```

## Wiki

- [How to install on shared hosting](https://github.com/farisc0de/Uploady/wiki/How-to-Install-on-a-shared-hosting)
- [How to install with Docker](https://github.com/farisc0de/Uploady/wiki/How-to-install-using-Docker)

## Roadmap (v4.0)

- [x] PhpFileUpload refactor
- [x] Upgrade Bootstrap to 5.3.3
- [x] Modernize the UI
- [x] ClamAV malware scanning support
- [ ] Report abuse dashboard
- [ ] S3 Bucket integration
- [ ] File tagging system
- [ ] Short URL generation

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT License - see [LICENSE](LICENSE) for details.

## Copyright

Developed by Faris AL-Otaibi - 2024-2025
