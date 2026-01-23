<?php

namespace Farisc0de\PhpFileUploading\Security;

use Farisc0de\PhpFileUploading\Logging\LoggerAwareTrait;

/**
 * ClamAV virus scanner implementation
 * 
 * Supports both clamd socket connection and clamscan command-line tool.
 *
 * @package PhpFileUploading
 */
class ClamAvScanner implements VirusScannerInterface
{
    use LoggerAwareTrait;

    private ?string $socketPath;
    private ?string $host;
    private ?int $port;
    private ?string $clamscanPath;
    private int $timeout;

    /**
     * @param string|null $socketPath Path to clamd socket (e.g., /var/run/clamav/clamd.sock)
     * @param string|null $host Clamd host for TCP connection
     * @param int|null $port Clamd port for TCP connection
     * @param string|null $clamscanPath Path to clamscan binary (fallback)
     * @param int $timeout Connection timeout in seconds
     */
    public function __construct(
        ?string $socketPath = null,
        ?string $host = null,
        ?int $port = null,
        ?string $clamscanPath = null,
        int $timeout = 30
    ) {
        $this->socketPath = $socketPath;
        $this->host = $host;
        $this->port = $port;
        $this->clamscanPath = $clamscanPath ?? $this->findClamscan();
        $this->timeout = $timeout;
    }

    public function scan(string $filePath): ScanResult
    {
        if (!file_exists($filePath)) {
            return ScanResult::error($filePath, 'File not found');
        }

        if (!is_readable($filePath)) {
            return ScanResult::error($filePath, 'File is not readable');
        }

        $startTime = microtime(true);

        // Try socket connection first
        if ($this->socketPath || ($this->host && $this->port)) {
            $result = $this->scanViaSocket($filePath);
            if ($result !== null) {
                $result->addMetadata('scan_time', microtime(true) - $startTime);
                return $result;
            }
        }

        // Fall back to command-line tool
        if ($this->clamscanPath) {
            $result = $this->scanViaCommand($filePath);
            $result->addMetadata('scan_time', microtime(true) - $startTime);
            return $result;
        }

        return ScanResult::error($filePath, 'No scanner available');
    }

    public function isAvailable(): bool
    {
        // Check socket
        if ($this->socketPath && file_exists($this->socketPath)) {
            return true;
        }

        // Check TCP connection
        if ($this->host && $this->port) {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, 1);
            if ($socket) {
                fclose($socket);
                return true;
            }
        }

        // Check command-line tool
        if ($this->clamscanPath && is_executable($this->clamscanPath)) {
            return true;
        }

        return false;
    }

    public function getVersion(): string
    {
        // Try to get version via socket
        if ($this->socketPath || ($this->host && $this->port)) {
            $version = $this->sendCommand('VERSION');
            if ($version) {
                return trim($version);
            }
        }

        // Try command-line
        if ($this->clamscanPath && is_executable($this->clamscanPath)) {
            $output = [];
            exec($this->clamscanPath . ' --version 2>&1', $output);
            if (!empty($output)) {
                return trim($output[0]);
            }
        }

        return 'ClamAV (version unknown)';
    }

    /**
     * Scan file via clamd socket
     */
    private function scanViaSocket(string $filePath): ?ScanResult
    {
        $response = $this->sendCommand('SCAN ' . $filePath);

        if ($response === null) {
            return null;
        }

        $this->logDebug('ClamAV response: {response}', ['response' => $response]);

        // Parse response
        // Format: /path/to/file: OK or /path/to/file: VirusName FOUND
        if (preg_match('/:\s*(.+)\s+FOUND$/i', $response, $matches)) {
            return ScanResult::infected($filePath, trim($matches[1]));
        }

        if (preg_match('/:\s*OK$/i', $response)) {
            return ScanResult::clean($filePath);
        }

        if (preg_match('/:\s*(.+)\s+ERROR$/i', $response, $matches)) {
            return ScanResult::error($filePath, trim($matches[1]));
        }

        return ScanResult::error($filePath, 'Unknown response: ' . $response);
    }

    /**
     * Scan file via clamscan command
     */
    private function scanViaCommand(string $filePath): ScanResult
    {
        $escapedPath = escapeshellarg($filePath);
        $command = $this->clamscanPath . ' --no-summary ' . $escapedPath . ' 2>&1';

        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $outputStr = implode("\n", $output);

        $this->logDebug('clamscan output: {output}, return code: {code}', [
            'output' => $outputStr,
            'code' => $returnCode
        ]);

        // Return codes: 0 = clean, 1 = virus found, 2 = error
        switch ($returnCode) {
            case 0:
                return ScanResult::clean($filePath);

            case 1:
                // Extract virus name
                if (preg_match('/:\s*(.+)\s+FOUND/i', $outputStr, $matches)) {
                    return ScanResult::infected($filePath, trim($matches[1]));
                }
                return ScanResult::infected($filePath, 'Unknown virus');

            default:
                return ScanResult::error($filePath, $outputStr ?: 'Scan failed');
        }
    }

    /**
     * Send command to clamd
     */
    private function sendCommand(string $command): ?string
    {
        $socket = $this->connect();
        if ($socket === null) {
            return null;
        }

        try {
            fwrite($socket, $command . "\n");

            $response = '';
            while (!feof($socket)) {
                $chunk = fread($socket, 4096);
                if ($chunk === false) {
                    break;
                }
                $response .= $chunk;
            }

            return $response;
        } finally {
            fclose($socket);
        }
    }

    /**
     * Connect to clamd
     */
    private function connect()
    {
        $socket = null;

        // Try Unix socket
        if ($this->socketPath) {
            $socket = @fsockopen('unix://' . $this->socketPath, -1, $errno, $errstr, $this->timeout);
        }

        // Try TCP connection
        if ($socket === false && $this->host && $this->port) {
            $socket = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        }

        if ($socket === false) {
            $this->logError('Failed to connect to ClamAV: {error}', ['error' => $errstr ?? 'Unknown error']);
            return null;
        }

        stream_set_timeout($socket, $this->timeout);
        return $socket;
    }

    /**
     * Find clamscan binary
     */
    private function findClamscan(): ?string
    {
        $paths = [
            '/usr/bin/clamscan',
            '/usr/local/bin/clamscan',
            '/opt/clamav/bin/clamscan',
        ];

        foreach ($paths as $path) {
            if (is_executable($path)) {
                return $path;
            }
        }

        // Try which command
        $output = [];
        exec('which clamscan 2>/dev/null', $output);
        if (!empty($output) && is_executable($output[0])) {
            return $output[0];
        }

        return null;
    }
}
