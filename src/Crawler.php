<?php

namespace Spatie\HttpStatusCheck;

use Arachnid\Crawler as BaseCrawler;

class Crawler extends BaseCrawler
{
    /**
     * @var callable
     */
    protected $afterTraverseSingle;

    /**
     * @param string $baseUrl
     * @param int $maxDepth
     * @param callable $callback
     */
    public function __construct($baseUrl, $maxDepth = 3, callable $callback)
    {
        parent::__construct($baseUrl, $maxDepth);

        $this->afterTraverseSingle = $callback;
    }

    /**
     * @param string $url
     * @param int $depth
     */
    protected function traverseSingle($url, $depth)
    {
        parent::traverseSingle($url, $depth);

        $hash = $this->getPathFromUrl($url);

        call_user_func_array($this->afterTraverseSingle, [$this->links[$hash]]);
    }
}
