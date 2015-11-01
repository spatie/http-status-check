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
        $this->output->writeln($response->getStatusCode() . ' - ' . $url);
        
        $this->crawledUrls[$response->getStatusCode()][] = $url;
    }

    public function displaySummary()
    {
        $this->output->writeln('');
        $this->output->writeln('Crawling summary');
        $this->output->writeln('----------------');

        foreach($this->crawledUrls as $statusCode => $urls) {
            $this->output->writeln('Crawled ' . count($urls) . ' url(s) with statuscode ' . $statusCode);
        }
    }

}
