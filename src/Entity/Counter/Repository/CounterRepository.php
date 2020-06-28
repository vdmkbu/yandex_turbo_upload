<?php

namespace App\Entity\Counter\Repository;


class CounterRepository implements CounterRepositoryInterface
{
    public function getYandexCounterId(): string
    {
        return "yandex_id";
    }

    public function getGoogleCounterId(): string
    {
        return "google_id";
    }

    public function getRamblerCounterId(): string
    {
       return "rambler_id";
    }

    public function getLiveInternetCounterId(): string
    {
        return "li_id";
    }

}