<?php
namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;

class ArchivoLis
{
    public function __construct(
        private string $eventCode,
        private ?string $eventName,
        private ?float $eventMagnitude,
        private ?DateTimeInterface $eventDate
    ) {
    }

    public static function fromRow(array $row): self
    {
        $date = null;
        if (!empty($row['event_date'])) {
            $date = new DateTimeImmutable((string) $row['event_date']);
        }

        $magnitude = null;
        if (isset($row['event_magnitude']) && $row['event_magnitude'] !== '') {
            $magnitude = (float) $row['event_magnitude'];
        }

        return new self(
            eventCode: (string) ($row['event_code'] ?? ''),
            eventName: $row['event_name'] ?? null,
            eventMagnitude: $magnitude,
            eventDate: $date,
        );
    }

    public function getEventCode(): string
    {
        return $this->eventCode;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function getEventMagnitude(): ?float
    {
        return $this->eventMagnitude;
    }

    public function getEventDate(): ?DateTimeInterface
    {
        return $this->eventDate;
    }
}
