<?php

namespace Emmedy\H5PBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanUpFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('h5p-bundle:cleanup-files')
            ->addArgument('location', InputArgument::OPTIONAL, 'The location of the files to clean up.')
            ->setDescription('Include the assets from the h5p vendor bundle in the public resources directory of this bundle.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cleanupFiles($input);
    }

    private function cleanupFiles(InputInterface $input)
    {
        $location = $input->getArgument('location');
        if (!$location) {
            $location = $this->getContainer()->get('emmedy_h5p.options')->getAbsoluteH5PPath() . '/editor';
        }
        \H5PCore::deleteFileTree($location);
    }
}