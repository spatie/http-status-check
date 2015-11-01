<?php

namespace Spatie\HttpStatusCheck\CrawlProfile;

use Spatie\HttpStatusCheck\Url;

interface CrawlProfile
{
    /**
     * @param Url $url
     * @return bool
     */
    public function shouldCrawl(Url $url);

}