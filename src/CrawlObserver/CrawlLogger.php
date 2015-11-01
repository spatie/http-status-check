<?php

namespace Spatie\HttpStatusCheck\CrawlObserver;

use GuzzleHttp\Psr7\Response;
use Spatie\HttpStatusCheck\Url;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlLogger implements CrawlObserver
{
    /**
     * @var OutputInterface
     */
    protected $output;

    protected $crawledUrls = [];

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param Url $url
     *
     * @return mixed
     */
    public function willCrawl(Url $url)
    {
    }

    public function haveCrawled(Url $url, Response $response)
    {
        $statusCode = $response->getStatusCode();

        $colorTag = $this->getColorTagForStatusCode($statusCode);

        $timestamp = date('Y-m-d H:i:s');

        $this->output->writeln("<{$colorTag}>[{$timestamp}] {$response->getStatusCode()} - {$url}</{$colorTag}>");

        $this->crawledUrls[$response->getStatusCode()][] = $url;
    }

    public function displaySummary()
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

    public function getColorTagForStatusCode($code)
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
