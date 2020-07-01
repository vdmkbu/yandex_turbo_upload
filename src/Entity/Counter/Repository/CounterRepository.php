<?php

namespace App\Entity\Counter\Repository;


class CounterRepository implements CounterRepositoryInterface
{
    const YANDEX_ID = "28982035";
    const GOOGLE_ID = "UA-107067500-1";
    const RAMBLER_ID = "4457193";
    const LI_ID = "lentachel.ru";

    public function getYandexCounterId(): string
    {
        return self::YANDEX_ID;
    }

    public function getGoogleCounterId(): string
    {
        return self::GOOGLE_ID;
    }

    public function getRamblerCounterId(): string
    {
       return self::RAMBLER_ID;
    }

    public function getLiveInternetCounterId(): string
    {
        return self::LI_ID;
    }

}