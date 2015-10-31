<?php

namespace Spatie\HttpStatusCheck\Test;

use PHPUnit_Framework_TestCase;
use Spatie\HttpStatusCheck\Url;

class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Url
     */
    protected $testUrl;

    public function setUp()
    {
        $this->testUrl = new Url('https://spatie.be/opensource');

        parent::setUp();
    }

    /**
     * @test
     */
    public function is_can_parse_an_url()
    {
        $this->assertEquals('https', $this->testUrl->scheme);
        $this->assertEquals('spatie.be', $this->testUrl->host);
        $this->assertEquals('/opensource', $this->testUrl->path);
    }

    /**
     * @test
     */
    public function it_can_be_converted_to_a_string()
    {
        $this->assertEquals('https://spatie.be/opensource', (string)$this->testUrl);
    }

    /**
     * @test
     */
    public function it_can_determine_if_an_url_is_relative()
    {
        $url = new Url('/opensource');

        $this->assertTrue($url->isRelative());

        $url = new Url($this->testUrl);

        $this->assertFalse($url->isRelative());
    }
}
