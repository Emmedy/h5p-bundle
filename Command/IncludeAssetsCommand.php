<?php

namespace Emmedy\H5PBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->copy($fromDir, $toDir, $coreSubDir, $coreDirs);

        $editorSubDir = "h5p-editor/";
        $editorDirs = ["ckeditor", "images", "language", "libs", "scripts", "styles"];
        $this->copy($fromDir, $toDir, $editorSubDir, $editorDirs);
    }

    private function copy($fromDir, $toDir, $subDir, $subDirs)
    {
        $filesystem = new Filesystem();
        foreach ($subDirs as $dir) {
            $filesystem->mirror($fromDir . $subDir . $dir, $toDir . $subDir . $dir);
        }
    }
}