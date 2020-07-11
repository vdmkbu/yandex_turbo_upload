<?php

namespace App\Entity\News\Repository;


use App\Entity\Site\Repository\SiteRepository;
use PDO;
use Twig\Environment;

class NewsRepository implements NewsRepositoryInterface
{
    CONST DEFAULT_AUTHOR = 'ЛентаЧел';
    CONST DEFAULT_CATEGORY = 'Новости';
    CONST IMAGE_PATH = '/netcat_files/';
    CONST DEFAULT_IMAGE = '/images/no_img_lenta-chel-2.jpg';
    CONST MULTIFIELD_ID = 2574;

    private $connection;
    private $data;
    private $twig;

    public function __construct(PDO $connection, Environment $twig)
    {
        $this->connection = $connection;
        $this->twig = $twig;
    }

    public function find(int $id)
    {
        $stmt = $this->connection->prepare('SELECT 
                                              m.*, 
                                              s.Subdivision_Name, 
                                              s.Hidden_URL,
                                              m2099.Name as author_name,
                                              category.Section_Name as category_name,
                                              GROUP_CONCAT(multi.Path) as photos
                                            FROM 
                                              Message2000 as m
                                            INNER JOIN 
                                              Subdivision as s ON (m.Subdivision_ID = s.Subdivision_ID)
                                            INNER JOIN
                                              Message2099 as m2099 ON (m.hasAuthor = m2099.Message_ID)
                                            INNER JOIN
                                              Classificator_Section as category ON (m.Section = category.Section_ID)
                                            INNER JOIN
                                              Multifield as multi ON (m.Message_ID = multi.Message_ID)
                                            WHERE
                                              multi.Field_ID = :field_id
                                              AND 
                                              m.Message_ID = :id');

        $stmt->execute([':id' => $id, ':field_id' => self::MULTIFIELD_ID]);

        $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $this->data;
    }

    public function getName(): string
    {
        return $this->data[0]->Name ?? '';
    }

    public function getLink(): string
    {
        $host = (new SiteRepository($this->connection))->getLink(1);

        list($date, $time) = explode(' ', $this->data[0]->Date);
        list($year, $month, $day) = explode('-', $date);

        return "https://" .
                $host .
                $this->data[0]->Hidden_URL .
                implode('/', [$year,
                              $month,
                              $day,
                              $this->data[0]->Keyword . ".html"
                ]);
    }

    public function getAmpLink(): string
    {
        $link = $this->getLink();
        return str_replace("/news/","/amp/", $link);
    }

    public function getAuthor(): string
    {
        return $this->data[0]->author_name ?: self::DEFAULT_AUTHOR;
    }

    public function getCategory(): string
    {
        return $this->data[0]->category_name ?: self::DEFAULT_CATEGORY;
    }

    public function getAnnounce(): string
    {
        return $this->data[0]->Announce;
    }

    public function getPreparedText(): string
    {
        $announce = $this->getAnnounce();
        $text = $this->getText();
        $instagram = $this->getInstagram();
        $multiphoto = $this->getMultiphoto();

        return preg_replace('/\s+/s',' ', $this->twig->render('announce.html.twig', ['announce'=>$announce], 'text/html') .
                                          $text .
                                          $instagram .
                                          $multiphoto);

    }

    public function getTextForTurbo(): string
    {
        $text = $this->getPreparedText();
        $title = $this->getName();
        $image = $this->getImage();
        $sign = $this->getSign();

        $image_url = $image['url'];

        return $this->twig->render('body.html.twig', [
            'image' => $image_url,
            'sign' => $sign,
            'title' => $title,
            'text' => $text
        ], 'text/html');


    }

    public function getDate(): string
    {
        return strtotime($this->data[0]->Date ? $this->data[0]->Date : $this->data[0]->Created);
    }

    public function getImage(): array
    {

        if ($this->data[0]->PictureBig) {

            list(,$type,$length,$path) = explode(':', $this->data[0]->PictureBig);
            $path = self::IMAGE_PATH . $path;
        }
        else {
            $path = self::DEFAULT_IMAGE;
        }

        $host = (new SiteRepository($this->connection))->getLink(1);

        return [
            'url' => "https://" . $host . $path,
            'length' => $length ?: 1000,
            'type' => $type ?: "image/jpeg"
        ];
    }

    public function isTurbo(): bool
    {
        if ($this->data[0]->turbo) {
            $turbo = true;
        }
        else {
            $turbo = false;
        }

        if(!$this->isActive()) {
            $turbo = false;
        }

        return $turbo;
    }

    public function isActive(): bool
    {
        return $this->data[0]->Checked ? true : false;
    }

    public function isReadyForUpload(): bool
    {
        return $this->data[0]->Webmaster ? true : false;
    }

    public function getText(): string
    {
        return $this->data[0]->Text;
    }

    public function getInstagram(): string
    {
        return $this->data[0]->Instagram;
    }

    public function getSign(): string
    {
        return $this->data[0]->Sign;
    }

    public function getCreated(): string
    {
        return $this->data[0]->Created;
    }

    public function getLastUpdated(): string
    {
        return $this->data[0]->LastUpdated;
    }

    public function getMultiphoto(): string
    {
        $result = '';

        if ($photos = $this->data[0]->photos) {
            return $this->twig->render('gallery.html.twig', [
                'photos' => explode(',',$photos),
            ], 'text/html');
        }

        return $result;

    }


}