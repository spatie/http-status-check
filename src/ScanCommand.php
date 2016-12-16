<?php

namespace Spatie\HttpStatusCheck;

use Spatie\Crawler\Crawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ScanCommand extends Command
{
    protected function configure()
    {
        $this->setName('scan')
            ->setDescription('Check the http status code of all links on a website.')
            ->addArgument(
                'url',
                InputArgument::REQUIRED,
                'The url to check'
            )
            ->addOption(
                'concurrency',
                'c',
                InputOption::VALUE_REQUIRED,
                'The amount of concurrent connections to use',
                10
            )
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                'The file to write the scan log for non 2xx responses'
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseUrl = $input->getArgument('url');

        $output->writeln("Start scanning {$baseUrl}");
        $output->writeln('');

        $crawlLogger = new CrawlLogger($output);

        if ($input->getOption('output')) {
            $outputFile = $input->getOption('output');

            if (file_exists($outputFile)) {
                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion('Overwrite existing file? ', false);
                $crawlLogger->setOverwriteOutputFile($helper->ask($input, $output, $question));
            }

            $crawlLogger->setOutputFile($input->getOption('output'));
        }

        Crawler::create()
            ->setConcurrency($input->getOption('concurrency'))
            ->setCrawlObserver($crawlLogger)
            ->startCrawling($baseUrl);

        return 0;
    }
}
