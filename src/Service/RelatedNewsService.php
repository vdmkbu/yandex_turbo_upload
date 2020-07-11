<?php

namespace App\Service;


use App\Entity\News\Repository\NewsRepositoryInterface;
use GuzzleHttp\Client;

class RelatedNewsService
{
    CONST SOURCE = '/netcat/modules/default/service/top/top.json';

    private $newsRepository;
    private $client;

    public function __construct(NewsRepositoryInterface $newsRepository, Client $client)
    {
        $this->newsRepository = $newsRepository;
        $this->client = $client;
    }

    public function getTop()
    {

        $top = $this->client->get(self::SOURCE);
        $top = $top->getBody()->getContents();

        $top_slice = array_slice(json_decode($top), 0, 3);

        $top_storage = [];
        foreach($top_slice as $slice_id) {

            $this->newsRepository->find($slice_id);
            $name = $this->newsRepository->getName();
            $link = $this->newsRepository->getLink();
            $image = $this->newsRepository->getImage();
            $image_url = $image['url'];

            $top_storage[] = [
                'name' => $name,
                'link' => $link,
                'image' => ['url' => $image_url]
            ];
        }

        return $top_storage;
    }
}