<?php

namespace Spatie\HttpStatusCheck\CrawlObserver;

use GuzzleHttp\Psr7\Response;
use Spatie\HttpStatusCheck\Url;

interface CrawlObserver
{
    /**
     * Called when the crawl will crawl the url.
     *
     * @param \Spatie\HttpStatusCheck\Url $url
     */
    public function willCrawl(Url $url);

    /**
     * Called when the crawl will crawl has crawled the given url.
     *
     * @param \Spatie\HttpStatusCheck\Url $url
     * @param \GuzzleHttp\Psr7\Response   $response
     */
    public function haveCrawled(Url $url, Response $response);
}
