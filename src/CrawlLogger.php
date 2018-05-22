<?php

namespace Spatie\HttpStatusCheck;

use Spatie\Crawler\CrawlObserver;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlLogger extends CrawlObserver
{
    const UNRESPONSIVE_HOST = 'Host did not respond';

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $consoleOutput;

    /**
     * @var array
     */
    protected $crawledUrls = [];

    /**
     * @var string|null
     */
    protected $outputFile = null;

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $consoleOutput
     */
    public function __construct(OutputInterface $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * Called when the crawl will crawl the url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     */
    public function willCrawl(UriInterface $url)
    {
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling()
    {
        $this->consoleOutput->writeln('');
        $this->consoleOutput->writeln('Crawling summary');
        $this->consoleOutput->writeln('----------------');

        ksort($this->crawledUrls);

        foreach ($this->crawledUrls as $statusCode => $urls) {
            $colorTag = $this->getColorTagForStatusCode($statusCode);

            $count = count($urls);

            if (is_numeric($statusCode)) {
                $this->consoleOutput->writeln("<{$colorTag}>Crawled {$count} url(s) with statuscode {$statusCode}</{$colorTag}>");
            }

            if ($statusCode == static::UNRESPONSIVE_HOST) {
                $this->consoleOutput->writeln("<{$colorTag}>{$count} url(s) did have unresponsive host(s)</{$colorTag}>");
            }
        }

        $this->consoleOutput->writeln('');
    }

    protected function getColorTagForStatusCode(string $code): string
    {
        if ($this->startsWith($code, '2')) {
            return 'info';
        }

        if ($this->startsWith($code, '3')) {
            return 'comment';
        }

        return 'error';
    }

    /**
     * @param string|null $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public function startsWith($haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the filename to write the output log.
     *
     * @param string $filename
     */
    public function setOutputFile($filename)
    {
        $this->outputFile = $filename;
    }

    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ) {
        $statusCode = $response->getStatusCode();

        $reason = $response->getReasonPhrase();

        $colorTag = $this->getColorTagForStatusCode($statusCode);

        $timestamp = date('Y-m-d H:i:s');

        $message = "{$statusCode} {$reason} - ".(string) $url;

        if ($this->outputFile && $colorTag === 'error') {
            file_put_contents($this->outputFile, $message.PHP_EOL, FILE_APPEND);
        }

        $this->consoleOutput->writeln("<{$colorTag}>[{$timestamp}] {$message}</{$colorTag}>");

        $this->crawledUrls[$statusCode][] = $url;
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ) {
        $statusCode = self::UNRESPONSIVE_HOST;

        $reason = $requestException->getResponse()
            ? $requestException->getResponse()->getReasonPhrase()
            : $requestException->getMessage();

        $colorTag = $this->getColorTagForStatusCode($statusCode);

        $timestamp = date('Y-m-d H:i:s');

        $message = "{$statusCode}: {$reason} - ".(string) $url;

        if ($foundOnUrl) {
            $message .= " (found on {$foundOnUrl})";
        }

        if ($this->outputFile) {
            file_put_contents($this->outputFile, $message.PHP_EOL, FILE_APPEND);
        }

        $this->consoleOutput->writeln("<{$colorTag}>[{$timestamp}] {$message}</{$colorTag}>");

        $this->crawledUrls[$statusCode][] = $url;
    }
}
