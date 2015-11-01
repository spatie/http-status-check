<?php

namespace Spatie\HttpStatusCheck\CrawlProfile;

use Spatie\HttpStatusCheck\Url;

class CrawlAllUrls implements CrawlProfile
{

    /**
     * Determine if the given url should be crawled.
     *
     * @param Url $url
     * @return bool
     */
    public function shouldCrawl(Url $url)
    {
        return true;
    }
}