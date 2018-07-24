<?php

namespace Emmedy\H5PBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IncludeAssetsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('h5p-bundle:include-assets')
            ->setDescription('Include the assets from the h5p vendor bundle in the public resources directory of this bundle.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->includeAssets();
    }

    private function includeAssets()
    {
        $fromDir = $this->getContainer()->getParameter('kernel.project_dir')."/vendor/h5p/";
        $toDir = $this->getContainer()->get("file_locator")->locate("@EmmedyH5PBundle/Resources/public/h5p/");

        $coreSubDir = "h5p-core/";
        $coreDirs = ["fonts", "images", "js", "styles"];
        $this->createSymLinks($fromDir, $toDir, $coreSubDir, $coreDirs);

        $editorSubDir = "h5p-editor/";
        $editorDirs = ["ckeditor", "images", "language", "libs", "scripts", "styles"];
        $this->createSymLinks($fromDir, $toDir, $editorSubDir, $editorDirs);
    }

    private function createSymLinks($fromDir, $toDir, $subDir, $subDirs)
    {
        foreach ($subDirs as $dir) {
            symlink($fromDir . $subDir . $dir, $toDir . $subDir . $dir);
        }
    }
}