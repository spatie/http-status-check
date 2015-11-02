<?php

namespace Spatie\HttpStatusCheck;

use Psr\Http\Message\ResponseInterface;
use Spatie\Crawler\CrawlObserver;
use Spatie\Crawler\Url;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlLogger implements CrawlObserver
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    protected $crawledUrls = [];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Called when the crawl will crawl the url.
     *
     * @param \Spatie\Crawler\Url $url
     */
    public function willCrawl(Url $url)
    {
    }

    /**
     * Called when the crawl will crawl has crawled the given url.
     *
     * @param \Spatie\Crawler\Url                 $url
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function haveCrawled(Url $url, ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();

        $colorTag = $this->getColorTagForStatusCode($statusCode);

        $reason = $response->getReasonPhrase();

        $timestamp = date('Y-m-d H:i:s');

        $this->output->writeln("<{$colorTag}>[{$timestamp}] {$response->getStatusCode()} - {$reason} - {$url}</{$colorTag}>");

        $this->crawledUrls[$response->getStatusCode()][] = $url;
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling()
    {
        $this->output->writeln('');
        $this->output->writeln('Crawling summary');
        $this->output->writeln('----------------');

        ksort($this->crawledUrls);

        foreach ($this->crawledUrls as $statusCode => $urls) {
            $colorTag = $this->getColorTagForStatusCode($statusCode);

            $count = count($urls);

            $this->output->writeln("<{$colorTag}>Crawled {$count} url(s) with statuscode {$statusCode}</{$colorTag}>");
        }
    }

    /**
     * Get the color tag for the given status code.
     *
     * @param string $code
     *
     * @return string
     */
    protected function getColorTagForStatusCode($code)
    {
        if (starts_with($code, '2')) {
            return 'info';
        }

        if (starts_with($code, '3')) {
            return 'comment';
        }

        return 'error';
    }
}
