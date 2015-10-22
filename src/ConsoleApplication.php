<?php

namespace Spatie\HttpStatusCheck;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ConsoleApplication extends Application
{
    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'httpstatuscheck';
    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new HttpStatusCheckCommand();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();

        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
