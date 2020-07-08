<?php

namespace App\Entity\News\Repository;


interface NewsRepositoryInterface
{
    public function getName(): string;
    public function getLink(): string;
    public function getAmpLink(): string;
    public function getAuthor(): string;
    public function getCategory(): string;
    public function getAnnounce(): string;
    public function getPreparedText(): string;
    public function getTextForTurbo(): string;
    public function getDate(): string;
    public function getImage(): array;
    public function isTurbo(): bool;

}