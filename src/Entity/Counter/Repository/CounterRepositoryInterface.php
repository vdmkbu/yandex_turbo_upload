<?php

namespace App\Entity\Counter\Repository;


interface CounterRepositoryInterface
{
    public function getYandexCounterId(): string;
    public function getGoogleCounterId(): string;
    public function getRamblerCounterId(): string;
    public function getLiveInternetCounterId(): string;

}