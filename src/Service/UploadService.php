<?php

namespace App\Service;


use App\Entity\Counter\Repository\CounterRepositoryInterface;
use App\Entity\News\Repository\NewsRepositoryInterface;
use App\Entity\Site\Repository\SiteRepositoryInterface;
use App\Service\API\TurboApi;
use App\Service\TurboPageGenerator\Item;
use sokolnikov911\YandexTurboPages\Channel;
use sokolnikov911\YandexTurboPages\Counter;
use sokolnikov911\YandexTurboPages\Feed;
use sokolnikov911\YandexTurboPages\RelatedItem;
use sokolnikov911\YandexTurboPages\RelatedItemsList;

class UploadService
{
    CONST SITE_ID = 1;

    private $api;
    private $siteRepository;
    private $counterRepository;
    private $newsRepository;
    private $relatedNewsService;

    public function __construct(TurboApi $api,
                                SiteRepositoryInterface $siteRepository,
                                CounterRepositoryInterface $counterRepository,
                                NewsRepositoryInterface $newsRepository,
                                RelatedNewsService $relatedNewsService)
    {
        $this->api = $api;
        $this->siteRepository = $siteRepository;
        $this->counterRepository = $counterRepository;
        $this->newsRepository = $newsRepository;
        $this->relatedNewsService = $relatedNewsService;
    }

    public function upload(array $data)
    {
        $this->api->requestUserId();
        $this->api->setHostId('https:'.getenv('TURBO_API_HOST').':443');
        $this->api->requestUploadAddress();

        $feed = new Feed();
        $channel = new Channel();

        $channel_title = $this->siteRepository->getTitle(self::SITE_ID);
        $channel_link = $this->siteRepository->getLink(self::SITE_ID);
        $channel_description = $this->siteRepository->getDescription(self::SITE_ID);
        $channel_language = $this->siteRepository->getLanguage(self::SITE_ID);

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

        $related_storage = $this->relatedNewsService->getTop();

        foreach($related_storage as $related_item) {


            $relatedItem = new RelatedItem($related_item['name'], $related_item['link'],
                $related_item['image']['url']);
            $relatedItem->appendTo($relatedItemsList);

        }

        foreach($data['messages'] as $message) {

            // для каждого ID новости получим данные
            $this->newsRepository->find($message);

            // если новость была добавлена меньше, чем 6 часов, то при обновлении не передаем в API
            // проверяем только если не передан параметр batch (для добавления старых новостей, игноруруем проверку поля Webmaster)
            if($data['prod'] && !$data['batch'] && !$this->newsRepository->isReadyForUpload()) {
                continue;
            }

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

        if ($data['prod'] == 1) {

            $result = $this->api->uploadRss($feed);
            return json_decode($result['response']);

        }
        else {
            return [
              'feed' => $feed
            ];
        }





    }

    public function delete(array $data)
    {
        $this->api->requestUserId();
        $this->api->setHostId('https:'.getenv('TURBO_API_HOST').':443');
        $this->api->requestUploadAddress();

        $feed = new Feed();
        $channel = new Channel();

        $channel_title = $this->siteRepository->getTitle(self::SITE_ID);
        $channel_link = $this->siteRepository->getLink(self::SITE_ID);
        $channel_description = $this->siteRepository->getDescription(self::SITE_ID);
        $channel_language = $this->siteRepository->getLanguage(self::SITE_ID);

        $channel
            ->title($channel_title)
            ->link($channel_link)
            ->description($channel_description)
            ->language($channel_language)
            ->appendTo($feed);

        foreach($data['messages'] as $message) {

            $this->newsRepository->find($message);

            $item = new Item(false);
            $link = $this->newsRepository->getLink();
            $item->link($link)
                ->appendTo($channel);
        }

        $feed  = $feed->render();

        if ($data['prod'] == 1) {

            $result = $this->api->uploadRss($feed);
            return json_decode($result['response']);

        }
        else {
            return [
                'feed' => $feed
            ];
        }

    }
}