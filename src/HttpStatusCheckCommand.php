<?php

namespace Spatie\HttpStatusCheck;

use Spatie\HttpStatusCheck\CrawlObserver\CrawlLogger;
use Spatie\HttpStatusCheck\CrawlProfile\CrawlAllUrls;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStatusCheckCommand extends Command
{
    protected function configure()
    {
        $this->setName('httpstatuscheck')
            ->setDescription('Check the status codes for a URL and all it\'s sublinks.')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The URL to check'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteCrawler = $this->getSiteCrawler();

        $crawlLogger = new CrawlLogger($output);

        $siteCrawler
            ->setObserver($crawlLogger)
            ->setCrawlProfile(new CrawlAllUrls())
            ->startCrawling(Url::create($input->getArgument('url')));

        $crawlLogger->displaySummary();

        return 0;
    }

    public function logResponse($response, $url)
    {
        echo $response->getStatusCode().'-'.$url.PHP_EOL;
    }

    public function getSiteCrawler()
    {
        $client = new \GuzzleHttp\Client();

        return new SiteCrawler($client);
    }
}
