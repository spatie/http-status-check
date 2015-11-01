<?php

namespace Spatie\HttpStatusCheck;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Spatie\HttpStatusCheck\CrawlObserver\CrawlObserver;
use Spatie\HttpStatusCheck\CrawlProfile\CrawlProfile;
use Spatie\HttpStatusCheck\Exceptions\InvalidBaseUrl;
use Spatie\HttpStatusCheck\Exceptions\InvalidUrl;
use Symfony\Component\DomCrawler\Crawler;

class SiteCrawler {

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Url;
     */
    protected $baseUrl;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $crawledUrls;

    /**
     * @var \Spatie\HttpStatusCheck\CrawlObserver\CrawlObserver
     */
    protected $observer;

    /**
     * @var \Spatie\HttpStatusCheck\CrawlProfile\CrawlProfile
     */
    protected $crawlProfile;

    public function __construct(Client $client)
    {

        $this->client = $client;
    }

    /**
     * Set the base url.
     *
     * @param mixed $baseUrl
     * @return SiteCrawler
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }


    /**
     * Set the crawl observer.
     *
     * @param CrawlObserver $observer
     * @return $this
     */
    public function setObserver(CrawlObserver $observer)
    {
        $this->observer = $observer;

        return $this;
    }

    /**
     * Set the crawl profile.
     *
     * @param CrawlProfile $crawlProfile
     */
    public function setCrawlProfile(CrawlProfile $crawlProfile)
    {
        $this->crawlProfile = $crawlProfile;
    }

    /**
     * Start the crawling process.
     *
     * @param Url $baseUrl
     * @throws InvalidBaseUrl
     */
    public function startCrawling(Url $baseUrl)
    {
        if ($baseUrl->isRelative()) throw new InvalidBaseUrl();

        $this->baseUrl = $baseUrl;

        $this->crawlUrl($baseUrl);

    }

    /**
     * Crawl the given url.
     *
     * @param Url $url
     */
    protected function crawlUrl(Url $url)
    {
        if (! $this->crawlProfile->shouldCrawl($url)) return;

        $this->observer->willCrawl($url);

        try {
            $response = $this->client->request('GET', (string)$url);

        }
        catch(RequestException $exception)
        {
            $response = $exception->getResponse();
        }
        $this->observer->haveCrawled($url, $response);


        $this->crawledUrls->push($url);

        if ($url->host === $this->baseUrl->host) {
            $this->crawlAllLinks($response->getBody()->getContents());
        }

    }

    /**
     * Crawl all links in the given html.
     *
     * @param $html
     */
    protected function crawlAllLinks($html)
    {
        $allLinks = $this->getAllLinks($html);

        collect($allLinks)
            ->filter(function(Url $url) {
                return ! $url->isEmailUrl();
            })
            ->map(function(Url $url) {
                return $this->normalizeUrl($url);
            })
            ->filter(function(Url $url) {
                return ! $this->hasAlreadyCrawled($url);
            })
            ->filter(function(Url $url) {
                return $this->crawlProfile->shouldCrawl($url);
            })
            ->map(function(Url $url) {
                $this->crawlUrl($url);
            });
    }

    /**
     * Get all links in the given html.
     *
     * @param string $html
     * @return \Spatie\HttpStatusCheck\Url[]
     */
    protected function getAllLinks($html)
    {
        $crawler = new Crawler($html);

        return collect($crawler->filterXpath('//a')->extract(['href']))->map(function ($url) {
            return Url::create($url);
        });
    }


    /**
     * Determine if the crawled has already crawled the given url.
     *
     * @param Url $url
     * @return bool
     */
    protected function hasAlreadyCrawled(Url $url)
    {
        foreach($this->crawledUrls as $crawledUrl) {
            if ((string)$crawledUrl == (string) $url) return true;
        }

        return false;
    }

    /**
     * Normalize the given url.
     *
     * @param Url $url
     * @return $this
     */
    protected function normalizeUrl(Url $url)
    {

        if ($url->isRelative())
        {
            $url

                ->setHost($this->baseUrl->host)
                ->setScheme($this->baseUrl->scheme);
        }

        return $url->removeFragment();
    }



}