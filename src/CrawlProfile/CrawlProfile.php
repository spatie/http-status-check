<?php

namespace Spatie\HttpStatusCheck\CrawlProfile;

use Spatie\HttpStatusCheck\Url;

interface CrawlProfile
{
    /**
     * Determine if the given url should be crawled.
     *
     * @param \Spatie\HttpStatusCheck\Url $url
     *
     * @return bool
     */
    public function shouldCrawl(Url $url);
}
