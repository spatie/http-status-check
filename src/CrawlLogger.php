<?php

namespace Spatie\HttpStatusCheck;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObserver;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlLogger extends CrawlObserver
{
    const UNRESPONSIVE_HOST = 'Host did not respond';
    const REDIRECT = 'Redirect';

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
     * @param string|null  $haystack
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
        if ($this->addRedirectedResult($url, $response, $foundOnUrl)) {
            return;
        }

        // response wasnt a redirect so lets add it as a standard result
        $this->addResult(
            (string) $url,
            (string) $foundOnUrl,
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ) {
        if ($response = $requestException->getResponse()) {
            $this->crawled($url, $response, $foundOnUrl);
        } else {
            $this->addResult((string) $url, (string) $foundOnUrl, '---', self::UNRESPONSIVE_HOST);
        }
    }

    public function addResult($url, $foundOnUrl, $statusCode, $reason)
    {
        /*
        * don't display duplicate results
        * this happens if a redirect is followed to an existing page
        */
        if (isset($this->crawledUrls[$statusCode]) && in_array($url, $this->crawledUrls[$statusCode])) {
            return;
        }

        $colorTag = $this->getColorTagForStatusCode($statusCode);

        $timestamp = date('Y-m-d H:i:s');

        $message = "{$statusCode} {$reason} - ".(string) $url;

        if ($foundOnUrl && $colorTag === 'error') {
            $message .= " (found on {$foundOnUrl})";
        }

        if ($this->outputFile && $colorTag === 'error') {
            file_put_contents($this->outputFile, $message.PHP_EOL, FILE_APPEND);
        }

        $this->consoleOutput->writeln("<{$colorTag}>[{$timestamp}] {$message}</{$colorTag}>");

        $this->crawledUrls[$statusCode][] = $url;
    }

    /*
    * https://github.com/guzzle/guzzle/blob/master/docs/faq.rst#how-can-i-track-redirected-requests
    */
    public function addRedirectedResult(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ) {
        // if its not a redirect the return false
        if (!$response->getHeader('X-Guzzle-Redirect-History')) {
            return false;
        }

        // retrieve Redirect URI history
        $redirectUriHistory = $response->getHeader('X-Guzzle-Redirect-History');

        // retrieve Redirect HTTP Status history
        $redirectCodeHistory = $response->getHeader('X-Guzzle-Redirect-Status-History');

        // Add the initial URI requested to the (beginning of) URI history
        array_unshift($redirectUriHistory, (string) $url);

        // Add the final HTTP status code to the end of HTTP response history
        array_push($redirectCodeHistory, $response->getStatusCode());

        // Combine the items of each array into a single result set
        $fullRedirectReport = [];
        foreach ($redirectUriHistory as $key => $value) {
            $fullRedirectReport[$key] = ['location' => $value, 'code' => $redirectCodeHistory[$key]];
        }

        // Add the redirects and final URL as results
        foreach ($fullRedirectReport as $k => $redirect) {
            $this->addResult(
                (string) $redirect['location'],
                (string) $foundOnUrl,
                $redirect['code'],
                $k + 1 == count($fullRedirectReport) ? $response->getReasonPhrase() : self::REDIRECT
            );
        }

        return true;
    }
}
