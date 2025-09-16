<?php
namespace App\Framework;

class FileResponse
{
    public function __construct(
        private string $filePath,
        private string $downloadName,
        private string $mimeType = 'application/octet-stream',
        private bool $deleteAfterSend = false
    ) {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getDownloadName(): string
    {
        return $this->downloadName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function shouldDeleteAfterSend(): bool
    {
        return $this->deleteAfterSend;
    }
}
