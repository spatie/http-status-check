<?php

namespace Spatie\HttpStatusCheck\Test;

use PHPUnit_Framework_TestCase;

class ScanCommandTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $consoleLog;

    /** @var string */
    protected $outputFile;

    public function setUp()
    {
        parent::setUp();

        $this->consoleLog = __DIR__.'/temp/consoleLog.txt';
        $this->outputFile = __DIR__.'/temp/outputFile.txt';

        file_put_contents($this->consoleLog, PHP_EOL);
    }

    /** @test */
    public function it_can_scan_a_site()
    {
        exec('php '.__DIR__."/../http-status-check scan http://localhost:8080 > {$this->consoleLog}");

        $this->appearsInConsoleOutput([
            'Start scanning http://localhost:8080',
            '200 OK - http://localhost:8080/',
            '200 OK - http://localhost:8080/link1',
            '200 OK - http://localhost:8080/link2',
            '302 Found - http://localhost:8080/link4',
            '200 OK - http://example.com/',
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 5 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 302',
            'Crawled 1 url(s) with statuscode 404',
        ]);
    }

    /** @test */
    public function it_can_scan_only_internal_links()
    {
        exec('php '.__DIR__."/../http-status-check scan http://localhost:8080 -x > {$this->consoleLog}");

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
    public function it_can_write_urls_with_errors_to_an_output_file()
    {
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }

        exec('php '.__DIR__."/../http-status-check scan --output {$this->outputFile} http://localhost:8080");

        $this->assertFileEquals(__DIR__.'/fixtures/output.txt', $this->outputFile);
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
            $consoleLogContent = file_get_contents($this->consoleLog);

            $this->assertEquals(1, substr_count($consoleLogContent, $text.PHP_EOL), "Did not find `{$text}` in the log");
        }
    }
}
