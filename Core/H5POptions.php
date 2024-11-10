<?php

namespace Studit\H5PBundle\Core;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Studit\H5PBundle\Entity\Option;

class H5POptions
{
    /**
     * @var array
     */
    private array|null $config;
    /**
     * @var array
     */
    private array|null $storedConfig = null;

    private $h5pPath;
    private $folderPath;
    private $projectRootDir;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * H5POptions constructor.
     * @param array|null $config
     * @param $projectRootDir
     * @param EntityManagerInterface $manager
     */
    public function __construct(?array $config, $projectRootDir, EntityManagerInterface $manager)
    {
        $this->config = $config;
        $this->projectRootDir = $projectRootDir;
        $this->manager = $manager;
    }

    /**
     * @param $name
     * @param $default
     * @return mixed|null
     */
    public function getOption($name, $default = null)
    {
        try {
            $this->retrieveStoredConfig();
        } catch (DriverException $e) {
        }

        if (isset($this->storedConfig[$name])) {
            return $this->storedConfig[$name];
        }
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return $default;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value): void
    {
        $this->retrieveStoredConfig();

        if (!isset($this->storedConfig[$name]) || $this->storedConfig[$name] !== $value) {
            $this->storedConfig[$name] = $value;
            $option = $this->manager->getRepository('Studit\H5PBundle\Entity\Option')->find($name);
            if (!$option) {
                $option = new Option($name);
            }
            $option->setValue($value);
            $this->manager->persist($option);
            $this->manager->flush();
        }
    }

    /**
     * @return void
     */
    private function retrieveStoredConfig(): void
    {
        if ($this->storedConfig === null) {
            $this->storedConfig = [];
            $options = $this->manager->getRepository('Studit\H5PBundle\Entity\Option')->findAll();
            foreach ($options as $option) {
                $this->storedConfig[$option->getName()] = $option->getValue();
            }
        }
    }

    /**
     * @param $set
     * @return mixed
     */
    public function getUploadedH5pFolderPath($set = null)
    {
        if (!empty($set)) {
            $this->folderPath = $set;
        }

        return $this->folderPath;
    }

    /**
     * @param $set
     * @return mixed
     */
    public function getUploadedH5pPath($set = null)
    {
        if (!empty($set)) {
            $this->h5pPath = $set;
        }

        return $this->h5pPath;
    }

    /**
     * Helper function to ensure storage_dir always starts with a '/'.
     *
     * @return string
     */
    private function formatStorageDir(): string
    {
        // Setup the default value to empty string
        $dir = $this->getOption('storage_dir', [""]);
        return $dir[0] === '/' ? $dir : "/{$dir}";
    }

    /**
     * @return string
     */
    public function getRelativeH5PPath(): string
    {
        return $this->formatStorageDir();
    }

    public function getAbsoluteH5PPathWithSlash(): string
    {
        return $this->getAbsoluteWebPath() . $this->formatStorageDir() . '/';
    }

    public function getAbsoluteH5PPath(): string
    {
        return rtrim($this->getAbsoluteWebPath(), '/') . $this->formatStorageDir();
    }

    public function getAbsoluteWebPath(): string
    {
        return $this->projectRootDir . '/' . $this->getOption('web_dir');
    }

    public function getLibraryFileUrl(string $libraryFolderName, string $fileName): string
    {
        return $this->getRelativeH5PPath() . "/libraries/$libraryFolderName/$fileName";
    }

    public function getH5PAssetPath(): string
    {
        return '/bundles/studith5p/h5p';
    }
}
