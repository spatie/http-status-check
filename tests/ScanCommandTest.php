<?php

namespace Spatie\HttpStatusCheck\Test;

use PHPUnit_Framework_TestCase;

class ScanCommandTest extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $logPath;

    public function setUp()
    {
        parent::setUp();

        $this->logPath = __DIR__.'/temp/consoleOutput.txt';

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
            '200 OK - http://localhost:8080/link3',
            '404 Not Found - http://localhost:8080/notExists (found on http://localhost:8080/link3)',
            'Crawling summary',
            'Crawled 4 url(s) with statuscode 200',
            'Crawled 1 url(s) with statuscode 404',
        ]);
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
