<?php

namespace Spatie\HttpStatusCheck\CrawlObserver;

use Spatie\HttpStatusCheck\Url;

class CrawlLogger implements CrawlObserver
{
    /**
     * @param Url $url
     *
     * @return mixed
     */
    public function willCrawl(Url $url)
    {
    }

    public function haveCrawled(Url $url, $response)
    {
        echo $response->getStatusCode().'-'.$url.PHP_EOL;
    }
}
