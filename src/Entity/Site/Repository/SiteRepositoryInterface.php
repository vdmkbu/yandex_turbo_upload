<?php

namespace App\Entity\Site\Repository;


interface SiteRepositoryInterface
{
    public function getTitle(int $id): string;
    public function getLink(int $id): string;
    public function getDescription(int $id): string;
    public function getLanguage(int $id): string;
}