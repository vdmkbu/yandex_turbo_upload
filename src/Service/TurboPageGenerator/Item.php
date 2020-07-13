<?php

namespace App\Service\TurboPageGenerator;


use sokolnikov911\YandexTurboPages\SimpleXMLElement;

class Item extends \sokolnikov911\YandexTurboPages\Item
{
    /** @var string */
    protected $ampLink;

    /** @var string */
    protected $description;

    /** @var array */
    protected $enclosure;

    public function amplink($ampLink)
    {
        $this->ampLink = $ampLink;
        return $this;
    }

    public function description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function enclosure($enclosure)
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    public function asXML(): SimpleXMLElement
    {
        $isTurboEnabled = $this->turbo ? 'true' : 'false';

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item turbo="' . $isTurboEnabled
            . '"></item>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        $this->title ? $xml->addChild('title', $this->title) : null;
        $this->link ? $xml->addChild('link', $this->link) : null;
        $this->ampLink ? $xml->addChild('amplink', $this->ampLink) : null;
        $this->turboContent ? $xml->addCdataChild('turbo:content', $this->turboContent, 'http://turbo.yandex.ru') : null;
        $this->pubDate ? $xml->addChildWithValueChecking('pubDate', date(DATE_RSS, $this->pubDate)) : null;
        $this->category ? $xml->addChildWithValueChecking('category', $this->category) : null;
        $this->author ? $xml->addChildWithValueChecking('author', $this->author) : null;
        $this->description ? $xml->addChildWithValueChecking('description', $this->description) : null;

        if ($this->enclosure['url']) {
            $enclosure = $xml->addChild('enclosure');
            $enclosure->addAttribute('url', $this->enclosure['url']);
            $enclosure->addAttribute('length', $this->enclosure['length']);
            $enclosure->addAttribute('type', $this->enclosure['type']);
        }

        $xml->addChildWithValueChecking('yandex:full-text', $this->fullText, 'http://news.yandex.ru');
        $xml->addChildWithValueChecking('turbo:topic', $this->turboTopic, 'http://turbo.yandex.ru');
        $xml->addChildWithValueChecking('turbo:source', $this->turboSource, 'http://turbo.yandex.ru');

        if ($this->relatedItemsList) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($this->relatedItemsList->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}