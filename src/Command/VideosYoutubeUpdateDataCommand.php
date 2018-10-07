<?php

namespace App\Command;

use App\Videos\Youtube\ImportFromYoutube;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VideosYoutubeUpdateDataCommand extends Command
{
    private $importFromYoutube;

    public function __construct(ImportFromYoutube $importFromYoutube)
    {
        $this->importFromYoutube = $importFromYoutube;

        parent::__construct();
    }


    protected static $defaultName = 'videos:youtube:update-data';

    protected function configure()
    {
        $this
            ->setDescription('Import videos data from Youtube provider')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->importFromYoutube->import();

//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
