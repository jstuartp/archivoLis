<?php
namespace App\Service;

use ZipArchive;

class EventFileService
{
    private const PGA_KEYS = ['PGA-N00E', 'PGA-UPDO', 'PGA+N90E'];

    public function __construct(private string $basePath)
    {
    }

    public function eventExists(string $eventCode): bool
    {
        return is_dir($this->getEventDirectory($eventCode));
    }

    public function getEventCodes(): array
    {
        if (!is_dir($this->basePath)) {
            return [];
        }

        $entries = array_filter(scandir($this->basePath), function ($entry) {
            if ($entry === '.' || $entry === '..') {
                return false;
            }

            return is_dir($this->basePath . '/' . $entry);
        });

        sort($entries, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($entries);
    }

    public function getEventSummary(string $eventCode): array
    {
        $files = $this->getLisFiles($eventCode);
        if (empty($files)) {
            return [];
        }

        $firstFile = $this->getEventDirectory($eventCode) . '/' . $files[0];
        $header = $this->parseLisHeader($firstFile);

        return [
            'epicenter' => $header['Epicenter'] ?? null,
            'eventDate' => $header['Event date'] ?? null,
            'eventMagnitude' => $header['Event Magnitude'] ?? null,
        ];
    }

    public function getEventFiles(string $eventCode): array
    {
        $files = [];
        foreach ($this->getLisFiles($eventCode) as $fileName) {
            $filePath = $this->getEventDirectory($eventCode) . '/' . $fileName;
            $header = $this->parseLisHeader($filePath);
            $pgaValues = [];
            foreach (self::PGA_KEYS as $key) {
                if (isset($header[$key])) {
                    $pgaValues[$key] = (float) str_replace(',', '.', (string) $header[$key]);
                }
            }

            $pgaMax = null;
            if (!empty($pgaValues)) {
                $pgaMax = max($pgaValues);
            }

            $files[] = [
                'name' => $fileName,
                'path' => $filePath,
                'station_code' => $header['Station Code'] ?? null,
                'pga_values' => $pgaValues,
                'pga_max' => $pgaMax,
            ];
        }

        return $files;
    }

    public function prepareDownload(string $eventCode, array $fileNames): array
    {
        $prepared = [];
        $eventDir = $this->getEventDirectory($eventCode);

        foreach ($fileNames as $fileName) {
            if (!is_string($fileName) || $fileName === '') {
                continue;
            }

            $safeName = basename($fileName);
            $filePath = $eventDir . '/' . $safeName;
            if (!is_file($filePath) || !str_ends_with(strtolower($safeName), '.lis')) {
                continue;
            }

            $prepared[] = [
                'path' => $filePath,
                'downloadName' => $safeName,
            ];
        }

        return $prepared;
    }

    public function createZipForFiles(string $eventCode, array $files): string
    {
        $zip = new ZipArchive();
        $zipPath = tempnam(sys_get_temp_dir(), 'lis_zip_');
        if ($zipPath === false) {
            throw new \RuntimeException('Unable to create temporary file for ZIP archive');
        }

        if ($zip->open($zipPath, ZipArchive::OVERWRITE | ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Unable to open ZIP archive for writing');
        }

        foreach ($files as $file) {
            $zip->addFile($file['path'], $eventCode . '/' . $file['downloadName']);
        }

        $zip->close();

        return $zipPath;
    }

    private function getLisFiles(string $eventCode): array
    {
        $eventDir = $this->getEventDirectory($eventCode);
        if (!is_dir($eventDir)) {
            return [];
        }

        $files = array_filter(scandir($eventDir), static function ($file) use ($eventDir) {
            if ($file === '.' || $file === '..') {
                return false;
            }

            $path = $eventDir . '/' . $file;
            return is_file($path) && str_ends_with(strtolower($file), '.lis');
        });

        sort($files, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($files);
    }

    private function parseLisHeader(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException(sprintf('Unable to open file "%s"', $filePath));
        }

        $header = [];
        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);
            if ($trimmed === '' || !str_contains($trimmed, ':')) {
                break;
            }

            [$key, $value] = array_map('trim', explode(':', $trimmed, 2));
            if ($key !== '') {
                $header[$key] = $value;
            }
        }

        fclose($handle);

        return $header;
    }

    private function getEventDirectory(string $eventCode): string
    {
        return rtrim($this->basePath, '/\\') . '/' . $eventCode;
    }
}
