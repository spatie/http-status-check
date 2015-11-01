<?php

namespace Spatie\HttpStatusCheck;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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

    protected $responseLogger = null;

    public function __construct(Client $client)
    {

        $this->client = $client;
    }

    /**
     * @param mixed $baseUrl
     * @return SiteCrawler
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @param $responseLogger
     * @return $this
     */
    public function setResponseLogger($responseLogger)
    {
        $this->responseLogger = $responseLogger;

        $this->crawledUrls = collect();

        return $this;
    }

    public function startCrawling($baseUrl)
    {
        if ($baseUrl->isRelative()) throw new InvalidBaseUrl();

        $this->baseUrl = $baseUrl;

        $this->crawlUrl($baseUrl);

    }

    protected function crawlUrl(Url $url)
    {
        try {
            $response = $this->client->request('GET', (string)$url);

        }
        catch(RequestException $exception)
        {
            $response = $exception->getResponse();
        }
        $this->logResponse($response, $url);



        
        $this->crawledUrls->push($url);

        if ($url->host === $this->baseUrl->host) {
            $this->crawlAllLinks($response->getBody()->getContents());
        }

    }

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
            ->map(function(Url $url) {
                $this->crawlUrl($url);
            });
    }

    /**
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

    protected function logResponse($response, $url)
    {
        if (is_null($this->responseLogger)) return;

        call_user_func_array($this->responseLogger, [$response, $url]);
    }

    protected function hasAlreadyCrawled(Url $url)
    {
        foreach($this->crawledUrls as $crawledUrl) {
            if ((string)$crawledUrl == (string) $url) return true;
        }

        return false;
    }

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