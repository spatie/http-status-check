<?php

namespace Spatie\HttpStatusCheck\Test;

use PHPUnit_Framework_TestCase;
use Spatie\HttpStatusCheck\ScanCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ScanCommandTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $logPath;

    public function setUp()
    {
        parent::setUp();

        $this->logPath = __DIR__.'/temp/consoleOutput.txt';
        $this->outputFile = __DIR__.'/temp/outputFile.txt';

        file_put_contents($this->logPath, PHP_EOL);
    }

    /** @test */
    public function it_can_scan_a_site()
    {
        exec('php '.__DIR__."/../http-status-check scan http://localhost:8080 > {$this->logPath}");

        $this->appearsInConsoleOutput([
            'Start scanning http://localhost:8080',
            '200 OK - http://localhost:8080/',
            '200 OK - http://localhost:8080/link1',
            '200 OK - http://localhost:8080/link2',
            '302 Found - http://localhost:8080/link4',
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 4 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 302',
            'Crawled 1 url(s) with statuscode 404',
        ]);
    }

    /** @test */
    public function it_can_scan_a_site_and_write_output_file()
    {
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }

        exec('php '.__DIR__."/../http-status-check scan --output {$this->outputFile} http://localhost:8080 > {$this->logPath}");

        $this->appearsInConsoleOutput([
            'Start scanning http://localhost:8080',
            '200 OK - http://localhost:8080/',
            '200 OK - http://localhost:8080/link1',
            '200 OK - http://localhost:8080/link2',
            '302 Found - http://localhost:8080/link4',
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 4 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 302',
            'Crawled 1 url(s) with statuscode 404',
        ]);

        $this->assertEquals(7, count(file($this->outputFile)));
    }

    /** @test */
    public function it_can_scan_a_site_and_write_output_file_and_appending()
    {
        /*
          Idea extracted from:
          http://symfony.com/doc/current/components/console/helpers/questionhelper.html#testing-a-command-that-expects-input
          http://marekkalnik.tumblr.com/post/32601882836/symfony2-testing-interactive-console-command
         */

        $app = new Application();
        $app->add(new ScanCommand());
        $command = $app->find('scan');

        $tester = new CommandTester($command);
        $tester->setInputs(['n']);
        $tester->execute([
            'command' => $command->getName(),
            '--output' => $this->outputFile,
            'url' => 'http://localhost:8080',
        ]);

        file_put_contents($this->logPath, $tester->getDisplay());

        $this->appearsInConsoleOutput([
            'Start scanning http://localhost:8080',
            '200 OK - http://localhost:8080/',
            '200 OK - http://localhost:8080/link1',
            '200 OK - http://localhost:8080/link2',
            '302 Found - http://localhost:8080/link4',
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 4 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 302',
            'Crawled 1 url(s) with statuscode 404',
        ]);

        $this->assertEquals(14, count(file($this->outputFile)));
    }

    /** @test */
    public function it_can_scan_a_site_and_write_output_file_and_confirm_overwrite()
    {
        $app = new Application();
        $app->add(new ScanCommand());
        $command = $app->find('scan');

        $tester = new CommandTester($command);
        $tester->setInputs(['y']);
        $tester->execute([
            'command' => $command->getName(),
            '--output' => $this->outputFile,
            'url' => 'http://localhost:8080',
        ]);

        file_put_contents($this->logPath, $tester->getDisplay());

        $this->appearsInConsoleOutput([
            'Start scanning http://localhost:8080',
            '200 OK - http://localhost:8080/',
            '200 OK - http://localhost:8080/link1',
            '200 OK - http://localhost:8080/link2',
            '302 Found - http://localhost:8080/link4',
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 4 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 302',
            'Crawled 1 url(s) with statuscode 404',
        ]);

        $this->assertEquals(7, count(file($this->outputFile)));
    }

    /**
     * @param string|array $texts
     */
    protected function appearsInConsoleOutput($texts)
    {
        if (! is_array($texts)) {
            $texts = [$texts];
        }

        foreach ($texts as $text) {
            $logContent = file_get_contents($this->logPath);

            $this->assertEquals(1, substr_count($logContent, $text.PHP_EOL), "Did not find `{$text}` in the log");
        }
    }
}
