<?php
namespace App\Repository;

use App\Entity\ArchivoLis;
use PDO;
use PDOException;

class ArchivoLisRepository
{
    private ?PDO $connection = null;

    public function __construct(?PDO $connection)
    {
        $this->connection = $connection;
    }

    public static function fromConfig(array $config): self
    {
        $pdo = null;
        try {
            $pdo = new PDO(
                $config['dsn'],
                $config['user'] ?? null,
                $config['password'] ?? null,
                $config['options'] ?? []
            );
        } catch (PDOException $exception) {
            // Connection errors are ignored so the application can continue without database data.
            $pdo = null;
        }

        return new self($pdo);
    }

    public function findByEventCode(string $eventCode): ?ArchivoLis
    {
        if ($this->connection === null) {
            return null;
        }

        $stmt = $this->connection->prepare(
            'SELECT event_code, event_name, event_magnitude, event_date FROM archivoLis WHERE event_code = :code LIMIT 1'
        );
        $stmt->execute(['code' => $eventCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? ArchivoLis::fromRow($row) : null;
    }

    public function findAllIndexedByCode(): array
    {
        if ($this->connection === null) {
            return [];
        }

        $stmt = $this->connection->query(
            'SELECT event_code, event_name, event_magnitude, event_date FROM archivoLis'
        );

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $entity = ArchivoLis::fromRow($row);
            $result[$entity->getEventCode()] = $entity;
        }

        return $result;
    }
}
