<?php

namespace Spatie\HttpStatusCheck;

use Symfony\Component\Console\Application;

class ConsoleApplication extends Application
{
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Http status check', '3.1.1');

        $this->add(new ScanCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
