<?php

namespace Emmedy\H5PBundle\Command;

use Emmedy\H5PBundle\Core\H5POptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class H5pBundleCleanUpFilesCommand extends Command
{
    /**
     * @var H5POptions $h5POptions
     */
    private H5POptions $h5POptions;

    public function __construct(H5POptions $h5POptions)
    {
        $this->h5POptions = $h5POptions;
        parent::__construct();
    }

    protected static $defaultName = 'h5p-bundle:cleanup-files';

    protected function configure(): void
    {
        $this
            ->addArgument('location', InputArgument::OPTIONAL, 'The location of the files to clean up.')
            ->setDescription(
                'Include the assets from the h5p vendor bundle in the public resources directory of this bundle.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cleanupFiles($input);
        return 0;
    }

    private function cleanupFiles(InputInterface $input): void
    {
        $location = $input->getArgument('location');
        if (!$location) {
            $location = $this->h5POptions->getAbsoluteH5PPath() . '/editor';
        }
        \H5PCore::deleteFileTree($location);
    }
}
