<?php

namespace App\Entity\Site\Repository;


class SiteRepository implements SiteRepositoryInterface
{
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