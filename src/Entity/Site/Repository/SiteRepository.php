<?php

namespace App\Entity\Site\Repository;


use PDO;

class SiteRepository implements SiteRepositoryInterface
{
    /**
     * @var PDO The database connection
     */
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getTitle(int $id): string
    {
        $stmt = $this->connection->prepare('SELECT `Catalogue_Name` FROM Catalogue WHERE Catalogue_ID = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn();
    }

    public function getLink(int $id): string
    {
        $stmt = $this->connection->prepare('SELECT `Domain` FROM Catalogue WHERE Catalogue_ID = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn();
    }

    public function getDescription(int $id): string
    {
        $stmt = $this->connection->prepare('SELECT `Description` FROM Catalogue WHERE Catalogue_ID = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn();
    }

    public function getLanguage(int $id): string
    {
        $stmt = $this->connection->prepare('SELECT `Language` FROM Catalogue WHERE Catalogue_ID = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn();
    }

}