<?php

namespace Spatie\HttpStatusCheck;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            )
            ->addOption(
                'depth',
                'd',
                InputOption::VALUE_REQUIRED,
                'The depth to check URL\'s, defaults to 5'
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
        $url = $input->getArgument('url');
        $depth = $input->getOption('depth') !== null ? (int) $input->getOption('depth') : 5;

        $crawler = new Crawler($url, $depth, function ($result) use ($output) {
            if (in_array($result['status_code'], [200, 201, 202, 203, 204, 205, 206])) {
                $output->writeln("<info>{$result['status_code']}</info> {$result['absolute_url']}");
                return;
            }

            if (in_array($result['status_code'], [301, 302, 303, 304, 305, 307])) {
                $output->writeln("<comment>{$result['status_code']}</comment> {$result['absolute_url']}");
                return;
            }

            $output->writeln("<error>{$result['status_code']}</error> {$result['absolute_url']}");
        });

        $crawler->traverse();

        return 0;
    }
}
