<?php

namespace Spatie\HttpStatusCheck;

class Url
{
    /**
     * @var null|string
     */
    public $scheme;

    /**
     * @var null|string
     */
    public $host;

    /**
     * @var null|string
     */
    public $path;

    public static function create($url)
    {
        return new static($url);
    }

    public function __construct($url)
    {
        $urlProperties = parse_url($url);

        collect(['scheme', 'host', 'path'])->map(function($property) use ($urlProperties) {

            if (! isset($urlProperties[$property])) return;

            $this->$property = strtolower($urlProperties[$property]);

        });
    }

    public function isRelative()
    {
        return is_null($this->host);
    }

    public function isEmailUrl()
    {
        return $this->scheme === 'mailto';
    }


    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function removeFragment()
    {
        $this->path = explode('#', $this->path)[0];

        return $this;
    }

    public function __toString()
    {
        $path = starts_with($this->path, '/') ? substr($this->path,1) : $this->path;

        return "{$this->scheme}://{$this->host}/{$path}";
    }
}