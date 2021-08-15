<?php

/**
 * @copyright EveryWorkflow. All rights reserved.
 */

declare(strict_types=1);

namespace EveryWorkflow\DeveloperBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoCommand extends Command
{
    protected static $defaultName = 'everyworkflow:info';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Information about every workflow project');
    }

    /**
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputOutput = new SymfonyStyle($input, $output);

        $inputOutput->title('EveryWorkflow v0.1.0-alpha');

        $inputOutput->block([
            '- Some info goes here',
            '- Some info goes here',
            '- Some info goes here',
            '- Some info goes here',
            '- Some info goes here',
            '- Some info goes here',
            '- Some info goes here',
        ]);

        $inputOutput->comment('End of info');

        return Command::SUCCESS;
    }
}
