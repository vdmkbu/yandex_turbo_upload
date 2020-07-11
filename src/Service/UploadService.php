<?php

namespace App\Service;


use App\Entity\Counter\Repository\CounterRepositoryInterface;
use App\Entity\News\Repository\NewsRepositoryInterface;
use App\Entity\Site\Repository\SiteRepositoryInterface;
use App\Http\JsonResponse;
use App\Service\API\TurboApi;
use App\Service\TurboPageGenerator\Item;
use sokolnikov911\YandexTurboPages\Channel;
use sokolnikov911\YandexTurboPages\Counter;
use sokolnikov911\YandexTurboPages\Feed;
use sokolnikov911\YandexTurboPages\RelatedItem;
use sokolnikov911\YandexTurboPages\RelatedItemsList;

class UploadService
{
    private $api;
    private $siteRepository;
    private $counterRepository;
    private $newsRepository;

    public function __construct(TurboApi $api,
                                SiteRepositoryInterface $siteRepository,
                                CounterRepositoryInterface $counterRepository,
                                NewsRepositoryInterface $newsRepository)
    {
        $this->api = $api;
        $this->siteRepository = $siteRepository;
        $this->counterRepository = $counterRepository;
        $this->newsRepository = $newsRepository;
    }

    public function upload(array $data)
    {
        $this->api->requestUserId();
        $this->api->setHostId('https:'.getenv('TURBO_API_HOST').':443');
        $this->api->requestUploadAddress();

        $feed = new Feed();
        $channel = new Channel();

        $channel_title = $this->siteRepository->getTitle(1);
        $channel_link = $this->siteRepository->getLink(1);
        $channel_description = $this->siteRepository->getDescription(1);
        $channel_language = $this->siteRepository->getLanguage(1);

        $yandex_id = $this->counterRepository->getYandexCounterId();
        $google_id = $this->counterRepository->getGoogleCounterId();
        $rambler_id = $this->counterRepository->getRamblerCounterId();
        $live_id = $this->counterRepository->getLiveInternetCounterId();

        $channel
            ->title($channel_title)
            ->link($channel_link)
            ->description($channel_description)
            ->language($channel_language)
            ->appendTo($feed);

        $googleCounter = new Counter(Counter::TYPE_GOOGLE_ANALYTICS, $google_id);
        $googleCounter->appendTo($channel);
        $yandexCounter = new Counter(Counter::TYPE_YANDEX, $yandex_id);
        $yandexCounter->appendTo($channel);
        $ramblerCounter = new Counter(Counter::TYPE_RAMBLER, $rambler_id);
        $ramblerCounter->appendTo($channel);
        $liveInternetCounter = new Counter(Counter::TYPE_LIVE_INTERNET, $live_id);
        $liveInternetCounter->appendTo($channel);

        // для каждого элемента в результирующем массиве создадим RelatedItem
        $relatedItemsList = new RelatedItemsList(true);

        $related_storage[] = [
            'name' => 'related_item_name_1',
            'link' => 'related_item_link_1',
            'image' => ['url' => 'related_item_image_1']
        ];

        $related_storage[] = [
            'name' => 'related_item_name_2',
            'link' => 'related_item_link_2',
            'image' => ['url' => 'related_item_image_2']
        ];

        foreach($related_storage as $related_item) {


            $relatedItem = new RelatedItem($related_item['name'], $related_item['link'],
                $related_item['image']['url']);
            $relatedItem->appendTo($relatedItemsList);

        }

        foreach($data['messages'] as $message) {

            // для каждого ID новости получим данные
            $this->newsRepository->find($message);
            $name = $this->newsRepository->getName();
            $link = $this->newsRepository->getLink();
            $amp_link = $this->newsRepository->getAmpLink();
            $category = $this->newsRepository->getCategory();
            $announce = $this->newsRepository->getAnnounce();
            $text = $this->newsRepository->getPreparedText();
            $turbo_text = $this->newsRepository->getTextForTurbo();
            $date = $this->newsRepository->getDate();
            $author = $this->newsRepository->getAuthor();
            $image = $this->newsRepository->getImage();
            $image_url = $image['url'];
            $image_length = $image['length'];
            $image_type = $image['type'];
            $item = new Item($this->newsRepository->isTurbo());

            $item
                ->title($name)
                ->link($link)
                ->amplink($amp_link)
                ->category($category)
                ->description(htmlspecialchars(strip_tags($announce)))
                ->enclosure(['url'=>$image_url,
                    'length'=>$image_length,
                    'type'=>$image_type])
                ->fullText(htmlspecialchars(strip_tags($text)))
                ->turboContent($turbo_text)
                ->pubDate($date)
                ->author($author)
                ->appendTo($channel);

            // присоединим к элементу
            $relatedItemsList->appendTo($item);
        }

        $feed  = $feed->render();

        $result = $this->api->uploadRss($feed);
        return json_decode($result['response']);




    }
}