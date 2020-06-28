<?php

namespace App\Entity\Site\Repository;


interface SiteRepositoryInterface
{
    public function getTitle(): string;
    public function getLink(): string;
    public function getDescription(): string;
    public function getLanguage(): string;
}