<?php

namespace Spatie\HttpStatusCheck;

use GuzzleHttp\Client;
use Spatie\HttpStatusCheck\Exceptions\InvalidUrl;
use Symfony\Component\DomCrawler\Crawler;

class SiteCrawler {

    /**
     * @var Client
     */
    protected $client;

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
        if (! $baseUrl->isValid()) throw new InvalidUrl;

        $this->baseUrl = $baseUrl;

        $this->crawlUrl($baseUrl);

    }

    protected function crawlUrl(Url $url)
    {
        $response = $this->client->request('GET', (string)$url);

        $this->logResponse($response, $url);
        
        $this->crawledUrls->push($url);

        if ($url->host === $this->baseUrl->host) {
            $this->crawlAllLinks($response->getBody());
        }

    }

    protected function crawlAllLinks($html)
    {
        $allLinks = $this->getAllLinks($html);

        collect($allLinks)
            ->filter(function($url) {
                return ! $this->hasAlreadyCrawled($url);
            })

            ->map(function($url) {
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



}