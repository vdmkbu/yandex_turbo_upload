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

    public function getTitle(): string
    {
        return "site_title";
    }

    public function getLink(): string
    {
        return "site_link";
    }

    public function getDescription(): string
    {
       return "site_description";
    }

    public function getLanguage(): string
    {
        return "site_language";
    }

}