<?php

namespace Spatie\HttpStatusCheck;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
                'The url to check'
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

    /**
     * Get the crawler.
     *
     * @return \Spatie\HttpStatusCheck\SiteCrawler
     */
    public function getSiteCrawler()
    {
        $client = new Client([
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::COOKIES => true]);

        return new SiteCrawler($client);
    }
}
