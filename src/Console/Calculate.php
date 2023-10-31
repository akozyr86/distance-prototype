<?php declare(strict_types=1);

namespace Akozyr\Distance\Console;

use Akozyr\Distance\Model\Mediator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Calculate extends Command
{
    private const FILEPATH_ARGUMENT = 'filename';
    private const MAP_ENGINE_OPTION = 'map-engine';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('distance:calculate');
        $this->setDescription('Calculate distance for the given list of addresses.'
            . 'Result can be found in the var folder.');
        $this->addArgument(self::FILEPATH_ARGUMENT,
            InputArgument::REQUIRED, 'Filename for csv file with addresses list.'
            . ' Should be placed inside var folder');
        $this->addOption(self::MAP_ENGINE_OPTION, '-m', InputOption::VALUE_OPTIONAL,
            'Choose map engine. Supports "google" (Google) and "pstack" (https://positionstack.com/) options.'
            . 'Google is default. For Positionstack check free API key.',
            'google');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathToCsv = $input->getArgument(self::FILEPATH_ARGUMENT);
        $mapEngine = $input->getOption(self::MAP_ENGINE_OPTION);

        $mediator = new Mediator();
        $mediator->process($pathToCsv, $mapEngine, $output);
        return self::SUCCESS;
    }
}