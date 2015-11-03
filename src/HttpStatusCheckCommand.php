<?php

namespace Spatie\HttpStatusCheck;

use Spatie\Crawler\Crawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HttpStatusCheckCommand extends Command
{
    protected function configure()
    {
        $this->setName('http-status-check')
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
        $baseUrl = $input->getArgument('url');

        $output->writeln("Start scanning {$baseUrl}");
        $output->writeln('');

        Crawler::create()
            ->setCrawlObserver(new CrawlLogger($output))
            ->startCrawling($baseUrl);

        return 0;
    }
}
