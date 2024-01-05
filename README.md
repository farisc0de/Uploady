<div align="center">
   <img src="https://i.ibb.co/qMSh4gN/UPLOADY-removebg-preview.png" alt="UPLOADY-removebg-preview" border="0">
</div>

![](https://img.shields.io/github/license/farisc0de/Uploady) ![](https://img.shields.io/github/v/release/farisc0de/Uploady) ![](https://img.shields.io/github/repo-size/farisc0de/Uploady) ![](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

# Uploady

Uploady is a Simple File Uploader Script with Multi File Upload Support

It comes with more than 20 features and you can set up it in less than 5 minutes.

The software is written in PHP and uses MySQL as a database.

And it is free and open source.

## Features

1. Simple to use and implement
2. 4 Protection levels
   - Mime Type
   - Extensions
   - Size
   - Forbidden names
3. Out Of The Box Functions
4. Bootstrap and jQuery over CDN
5. Multi-File Upload Support
6. Admin Panel with charts and stats
7. Drag and Drop Support
8. Role-based User Management with size limit for each role
9. Data collection [IP Address, Browser Information, OS, Country]
10. Image Manipulation
11. AdSense Support
12. Google Analytics Support
13. Pages Management
14. Multi-Languages Support
15. Custom CSS and JS support
16. Custom logo and favicon
17. Delete files after x days
18. Delete files after x downloads
19. Report abuse page
20. Social media sharing

## Screenshots

![](https://i.imgur.com/ropeZWD.png)

![](https://i.imgur.com/fTe1FCZ.png)

## Note

Change the permission of everything to 755 `chmod 755 -R uploady/`

## How to Install

1. Upload all files to your server
2. Modify config/config.php with your custom info
3. Change files and folders permission to 775
4. Run install.php
5. Enjoy (:

## Docker Deployment

Update config.php with connection string as below

```php
define("DB_HOST", "uploady");
define("DB_USER", "uploady");
define("DB_PASS", "uploady");
define("DB_NAME", "uploady");
```

Then use the below command to run the application

```bash
git clone https://github.com/farisc0de/Uploady
cd Uploady/
docker build . -t farisc0de/uploady
docker-compose up -d
```

## License

MIT

## Copyright

Developed by Faris AL-Otaibi - 2023
