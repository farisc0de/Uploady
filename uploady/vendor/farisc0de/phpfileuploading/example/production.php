<?php

/**
 * Production-ready file upload example
 * 
 * This example demonstrates how to use all the new features together
 * for a secure, production-ready file upload system.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Farisc0de\PhpFileUploading\File;
use Farisc0de\PhpFileUploading\Utility;
use Farisc0de\PhpFileUploading\UploadManager;
use Farisc0de\PhpFileUploading\UploadResult;
use Farisc0de\PhpFileUploading\Storage\LocalStorage;
use Farisc0de\PhpFileUploading\Validation\ValidationChain;
use Farisc0de\PhpFileUploading\Validation\Validators\ExtensionValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\MimeTypeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\SizeValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\FilenameValidator;
use Farisc0de\PhpFileUploading\Validation\Validators\ImageDimensionValidator;
use Farisc0de\PhpFileUploading\RateLimiting\FileRateLimiter;
use Farisc0de\PhpFileUploading\Security\ClamAvScanner;
use Farisc0de\PhpFileUploading\Security\NullScanner;
use Farisc0de\PhpFileUploading\Events\EventDispatcher;
use Farisc0de\PhpFileUploading\Events\UploadEvents;
use Farisc0de\PhpFileUploading\Events\FileEvent;
use Farisc0de\PhpFileUploading\Logging\FileLogger;
use Farisc0de\PhpFileUploading\Logging\LogLevel;

// Configuration
$config = [
    'upload_dir' => __DIR__ . '/uploads',
    'public_url' => 'https://example.com/uploads',
    'log_file' => __DIR__ . '/logs/upload.log',
    'rate_limit_dir' => __DIR__ . '/rate_limits',
    'max_uploads_per_minute' => 10,
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ],
    'max_file_size' => '10 MB',
    'max_image_width' => 4096,
    'max_image_height' => 4096,
    'forbidden_filenames' => [
        'shell.php', 'backdoor.php', '.htaccess', 'config.php'
    ],
];

// Ensure directories exist
foreach ([$config['upload_dir'], dirname($config['log_file']), $config['rate_limit_dir']] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Initialize components
$logger = new FileLogger($config['log_file'], LogLevel::INFO);

$storage = new LocalStorage(
    $config['upload_dir'],
    0755,
    0644,
    $config['public_url']
);
$storage->setLogger($logger);

$validator = new ValidationChain();
$validator
    ->addValidator(new FilenameValidator($config['forbidden_filenames']))
    ->addValidator(new ExtensionValidator($config['allowed_extensions']))
    ->addValidator(new MimeTypeValidator($config['allowed_mimes']))
    ->addValidator(new SizeValidator(null, $config['max_file_size']))
    ->addValidator(new ImageDimensionValidator(
        null,
        $config['max_image_width'],
        null,
        $config['max_image_height']
    ));
$validator->setLogger($logger);

$rateLimiter = new FileRateLimiter(
    $config['rate_limit_dir'],
    $config['max_uploads_per_minute'],
    60
);

// Use NullScanner if ClamAV is not available
// In production, configure ClamAvScanner with your ClamAV socket/host
$virusScanner = new NullScanner();
// $virusScanner = new ClamAvScanner('/var/run/clamav/clamd.sock');

$eventDispatcher = new EventDispatcher();
$eventDispatcher->setLogger($logger);

// Register event listeners
$eventDispatcher->addListener(UploadEvents::AFTER_UPLOAD, function (FileEvent $event) use ($logger) {
    $logger->info('File uploaded: {filename}', [
        'filename' => $event->get('filename'),
        'path' => $event->get('path'),
    ]);
});

$eventDispatcher->addListener(UploadEvents::UPLOAD_FAILED, function (FileEvent $event) use ($logger) {
    $logger->warning('Upload failed: {error}', [
        'error' => $event->get('error'),
        'filename' => $event->getFilename(),
    ]);
});

$eventDispatcher->addListener(UploadEvents::RATE_LIMIT_EXCEEDED, function (FileEvent $event) use ($logger) {
    $logger->warning('Rate limit exceeded for {identifier}', [
        'identifier' => $event->get('identifier'),
    ]);
});

// Create upload manager
$uploadManager = new UploadManager(
    $storage,
    $validator,
    $rateLimiter,
    $virusScanner,
    $eventDispatcher
);
$uploadManager->setLogger($logger);
$uploadManager->setHashFilenames(true);

// Handle upload request
$response = ['success' => false, 'message' => '', 'data' => null, 'files' => []];
$utility = new Utility();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        // Check if files were uploaded
        if (!isset($_FILES['files']) && !isset($_FILES['file'])) {
            throw new \RuntimeException('No files uploaded');
        }

        // Determine if single or multiple file upload
        $isMultiple = isset($_FILES['files']);
        $uploadedFiles = [];
        $failedFiles = [];

        if ($isMultiple) {
            // Normalize the $_FILES array for multiple uploads
            $normalizedFiles = $utility->normalizeFileArray($_FILES['files']);
            
            foreach ($normalizedFiles as $fileData) {
                if ($fileData['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                
                try {
                    $file = new File($fileData, $utility);
                    $result = $uploadManager->upload($file, 'user-uploads', $clientIp);
                    
                    if ($result->isSuccess()) {
                        $uploadedFiles[] = [
                            'filename' => $result->getStoredFilename(),
                            'original_name' => $result->getOriginalFilename(),
                            'url' => $result->getPublicUrl(),
                            'size' => $result->getFileSize(),
                            'mime_type' => $result->getMimeType(),
                            'hash' => $result->getFileHash(),
                        ];
                    } else {
                        $failedFiles[] = [
                            'original_name' => $fileData['name'],
                            'error' => $result->getError(),
                        ];
                    }
                    
                    // Set rate limit headers from last result
                    foreach ($result->getRateLimitHeaders() as $header => $value) {
                        header("{$header}: {$value}");
                    }
                } catch (\Exception $e) {
                    $failedFiles[] = [
                        'original_name' => $fileData['name'],
                        'error' => $e->getMessage(),
                    ];
                }
            }
            
            if (empty($uploadedFiles) && empty($failedFiles)) {
                throw new \RuntimeException('No files selected');
            }
            
            $totalFiles = count($uploadedFiles) + count($failedFiles);
            $successCount = count($uploadedFiles);
            
            if ($successCount === $totalFiles) {
                $response = [
                    'success' => true,
                    'message' => $successCount === 1 
                        ? 'File uploaded successfully' 
                        : "{$successCount} files uploaded successfully",
                    'data' => $uploadedFiles[0] ?? null,
                    'files' => $uploadedFiles,
                ];
            } elseif ($successCount > 0) {
                $response = [
                    'success' => true,
                    'message' => "{$successCount} of {$totalFiles} files uploaded successfully",
                    'data' => $uploadedFiles[0] ?? null,
                    'files' => $uploadedFiles,
                    'failed' => $failedFiles,
                ];
            } else {
                http_response_code(400);
                $response = [
                    'success' => false,
                    'message' => 'All uploads failed',
                    'data' => null,
                    'failed' => $failedFiles,
                ];
            }
        } else {
            // Single file upload (backward compatible)
            if ($_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new \RuntimeException('No file uploaded');
            }
            
            $file = new File($_FILES['file'], $utility);
            $result = $uploadManager->upload($file, 'user-uploads', $clientIp);

            // Set rate limit headers
            foreach ($result->getRateLimitHeaders() as $header => $value) {
                header("{$header}: {$value}");
            }

            if ($result->isSuccess()) {
                $response = [
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => [
                        'filename' => $result->getStoredFilename(),
                        'original_name' => $result->getOriginalFilename(),
                        'url' => $result->getPublicUrl(),
                        'size' => $result->getFileSize(),
                        'mime_type' => $result->getMimeType(),
                        'hash' => $result->getFileHash(),
                    ],
                    'files' => [[
                        'filename' => $result->getStoredFilename(),
                        'original_name' => $result->getOriginalFilename(),
                        'url' => $result->getPublicUrl(),
                        'size' => $result->getFileSize(),
                        'mime_type' => $result->getMimeType(),
                        'hash' => $result->getFileHash(),
                    ]],
                ];
            } else {
                http_response_code(400);
                $response = [
                    'success' => false,
                    'message' => $result->getError(),
                    'data' => null,
                ];
            }
        }

    } catch (\Farisc0de\PhpFileUploading\Exception\ValidationException $e) {
        http_response_code($e->getCode() === 1010 ? 429 : 400);
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
            'data' => ['context' => $e->getContext()],
        ];
    } catch (\Exception $e) {
        http_response_code(500);
        $response = [
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage(),
            'data' => null,
        ];
    }

    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production File Upload</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .upload-area:hover, .upload-area.dragover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        .upload-area svg {
            width: 48px;
            height: 48px;
            color: #667eea;
            margin-bottom: 15px;
        }
        .upload-area p {
            color: #666;
            margin-bottom: 10px;
        }
        .upload-area .formats {
            font-size: 12px;
            color: #999;
        }
        input[type="file"] {
            display: none;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }
        .file-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-size: 14px;
        }
        .file-info p {
            margin: 5px 0;
            color: #555;
        }
        .file-info strong {
            color: #333;
        }
        .progress {
            height: 4px;
            background: #eee;
            border-radius: 2px;
            margin-top: 15px;
            overflow: hidden;
            display: none;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
        }
        .selected-file {
            background: #f0f4ff;
            border-radius: 8px;
            padding: 12px;
            margin-top: 15px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        .selected-file.show {
            display: flex;
        }
        .selected-file .name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #333;
        }
        .selected-file .size {
            color: #666;
            font-size: 12px;
        }
        .file-list {
            max-height: 200px;
            overflow-y: auto;
            margin-top: 15px;
        }
        .file-item {
            background: #f0f4ff;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-item .name {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #333;
            font-size: 14px;
        }
        .file-item .size {
            color: #666;
            font-size: 12px;
        }
        .file-item .remove {
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .file-item .remove:hover {
            background: #ff3344;
        }
        .upload-mode-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .upload-mode-toggle button {
            flex: 1;
            padding: 10px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }
        .upload-mode-toggle button.active {
            border-color: #667eea;
            background: #f0f4ff;
            color: #667eea;
        }
        .upload-mode-toggle button:hover {
            border-color: #667eea;
        }
        .file-count {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Secure File Upload</h1>
        <p class="subtitle">Production-ready with validation, rate limiting & virus scanning</p>

        <?php if ($response['success']): ?>
            <div class="message success">
                <?= htmlspecialchars($response['message']) ?>
            </div>
            <?php if (!empty($response['files'])): ?>
                <?php foreach ($response['files'] as $index => $fileData): ?>
                <div class="file-info">
                    <p><strong>File <?= $index + 1 ?>:</strong></p>
                    <p><strong>Filename:</strong> <?= htmlspecialchars($fileData['filename']) ?></p>
                    <p><strong>Original:</strong> <?= htmlspecialchars($fileData['original_name']) ?></p>
                    <p><strong>Size:</strong> <?= number_format($fileData['size']) ?> bytes</p>
                    <p><strong>Type:</strong> <?= htmlspecialchars($fileData['mime_type']) ?></p>
                    <?php if (!empty($fileData['url'])): ?>
                    <p><strong>URL:</strong> <a href="<?= htmlspecialchars($fileData['url']) ?>" target="_blank">View File</a></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($response['failed'])): ?>
                <div class="message error" style="margin-top: 15px;">
                    <strong>Failed uploads:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($response['failed'] as $failed): ?>
                        <li><?= htmlspecialchars($failed['original_name']) ?>: <?= htmlspecialchars($failed['error']) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php elseif ($response['message']): ?>
            <div class="message error">
                <?= htmlspecialchars($response['message']) ?>
            </div>
        <?php endif; ?>

        <div class="upload-mode-toggle">
            <button type="button" id="singleMode" class="active">Single File</button>
            <button type="button" id="multiMode">Multiple Files</button>
        </div>

        <form method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-area" id="dropZone">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p id="dropText">Drag & drop your file here or click to browse</p>
                <span class="formats">Allowed: JPG, PNG, GIF, PDF, DOC, DOCX (max 10MB each)</span>
                <input type="file" name="file" id="fileInput" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                <input type="file" name="files[]" id="fileInputMulti" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" multiple style="display:none;">
            </div>

            <!-- Single file display -->
            <div class="selected-file" id="selectedFile">
                <span class="name" id="fileName"></span>
                <span class="size" id="fileSize"></span>
            </div>

            <!-- Multiple files display -->
            <div class="file-list" id="fileList" style="display:none;"></div>
            <div class="file-count" id="fileCount" style="display:none;"></div>

            <div class="progress" id="progress">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <button type="submit" class="btn" id="submitBtn">Upload File</button>
        </form>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileInputMulti = document.getElementById('fileInputMulti');
        const selectedFile = document.getElementById('selectedFile');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileList = document.getElementById('fileList');
        const fileCount = document.getElementById('fileCount');
        const dropText = document.getElementById('dropText');
        const form = document.getElementById('uploadForm');
        const progress = document.getElementById('progress');
        const progressBar = document.getElementById('progressBar');
        const submitBtn = document.getElementById('submitBtn');
        const singleModeBtn = document.getElementById('singleMode');
        const multiModeBtn = document.getElementById('multiMode');

        let isMultiMode = false;
        let selectedFiles = [];

        // Mode toggle
        singleModeBtn.addEventListener('click', () => {
            isMultiMode = false;
            singleModeBtn.classList.add('active');
            multiModeBtn.classList.remove('active');
            dropText.textContent = 'Drag & drop your file here or click to browse';
            submitBtn.textContent = 'Upload File';
            fileInput.style.display = '';
            fileInputMulti.style.display = 'none';
            fileList.style.display = 'none';
            fileCount.style.display = 'none';
            selectedFile.classList.remove('show');
            selectedFiles = [];
            fileInput.value = '';
            fileInputMulti.value = '';
        });

        multiModeBtn.addEventListener('click', () => {
            isMultiMode = true;
            multiModeBtn.classList.add('active');
            singleModeBtn.classList.remove('active');
            dropText.textContent = 'Drag & drop your files here or click to browse';
            submitBtn.textContent = 'Upload Files';
            fileInput.style.display = 'none';
            fileInputMulti.style.display = '';
            selectedFile.classList.remove('show');
            fileList.style.display = 'block';
            fileCount.style.display = 'block';
            selectedFiles = [];
            fileInput.value = '';
            fileInputMulti.value = '';
            updateFileList();
        });

        // Click to upload
        dropZone.addEventListener('click', () => {
            if (isMultiMode) {
                fileInputMulti.click();
            } else {
                fileInput.click();
            }
        });

        // Drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                if (isMultiMode) {
                    addFiles(e.dataTransfer.files);
                } else {
                    fileInput.files = e.dataTransfer.files;
                    updateSelectedFile();
                }
            }
        });

        // File selection - single
        fileInput.addEventListener('change', updateSelectedFile);

        // File selection - multiple
        fileInputMulti.addEventListener('change', () => {
            addFiles(fileInputMulti.files);
        });

        function addFiles(files) {
            for (let i = 0; i < files.length; i++) {
                // Avoid duplicates
                if (!selectedFiles.some(f => f.name === files[i].name && f.size === files[i].size)) {
                    selectedFiles.push(files[i]);
                }
            }
            updateFileList();
            updateFileInputFromList();
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updateFileList();
            updateFileInputFromList();
        }

        function updateFileInputFromList() {
            // Create a new DataTransfer to update the file input
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInputMulti.files = dt.files;
        }

        function updateFileList() {
            fileList.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const item = document.createElement('div');
                item.className = 'file-item';
                item.innerHTML = `
                    <span class="name">${escapeHtml(file.name)}</span>
                    <span class="size">${formatBytes(file.size)}</span>
                    <button type="button" class="remove" onclick="removeFile(${index})">&times;</button>
                `;
                fileList.appendChild(item);
            });
            
            if (selectedFiles.length > 0) {
                const totalSize = selectedFiles.reduce((sum, f) => sum + f.size, 0);
                fileCount.textContent = `${selectedFiles.length} file(s) selected (${formatBytes(totalSize)} total)`;
                fileCount.style.display = 'block';
            } else {
                fileCount.textContent = 'No files selected';
            }
        }

        function updateSelectedFile() {
            if (fileInput.files.length) {
                const file = fileInput.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatBytes(file.size);
                selectedFile.classList.add('show');
            } else {
                selectedFile.classList.remove('show');
            }
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Form submission with progress
        form.addEventListener('submit', function(e) {
            const hasFiles = isMultiMode ? selectedFiles.length > 0 : fileInput.files.length > 0;
            
            if (!hasFiles) {
                e.preventDefault();
                alert('Please select at least one file');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            progress.style.display = 'block';
        });
    </script>
</body>
</html>
