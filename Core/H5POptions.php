<?php

namespace Emmedy\H5PBundle\Core;


use Doctrine\ORM\EntityManager;
use Emmedy\H5PBundle\Entity\Option;

class H5POptions
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    private $storedConfig = null;

    private $h5pPath;
    private $folderPath;
    private $kernelRootDir;
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * H5POptions constructor.
     * @param array $config
     * @param $kernelRootDir
     * @param EntityManager $manager
     */
    public function __construct(array $config, $kernelRootDir, EntityManager $manager)
    {
        $this->config = $config;
        $this->kernelRootDir = $kernelRootDir;
        $this->manager = $manager;
    }

    public function getOption($name, $default = null)
    {
        $this->retrieveStoredConfig();

        if (isset($this->storedConfig[$name])) {
            return $this->storedConfig[$name];
        }
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return $default;
    }

    public function setOption($name, $value)
    {
        $this->retrieveStoredConfig();

        if (!isset($this->storedConfig[$name]) || $this->storedConfig[$name] !== $value) {
            $this->storedConfig[$name] = $value;
            $option = $this->manager->getRepository('EmmedyH5PBundle:Option')->find($name);
            if (!$option) {
                $option = new Option($name);
            }
            $option->setValue($value);
            $this->manager->persist($option);
            $this->manager->flush();
        }
    }

    public function getUploadedH5pFolderPath($set = null)
    {
        if (!empty($set)) {
            $this->folderPath = $set;
        }

        return $this->folderPath;
    }

    public function getUploadedH5pPath($set = null)
    {
        if (!empty($set)) {
            $this->h5pPath = $set;
        }

        return $this->h5pPath;
    }

    public function getRelativeH5PPath()
    {
        return $this->getOption('storage_path');
    }

    public function getAbsoluteH5PPath()
    {
        return $this->kernelRootDir . '/..' . $this->getOption('web_path') . '/' .$this->getOption('storage_path');
    }

    public function getLibraryFileUrl($libraryFolderName, $fileName)
    {
        return '/' . $this->getRelativeH5PPath() . "/libraries/$libraryFolderName/$fileName";
    }

    private function retrieveStoredConfig()
    {
        if ($this->storedConfig === null) {
            $this->storedConfig = [];
            $options = $this->manager->getRepository('EmmedyH5PBundle:Option')->findAll();
            foreach ($options as $option) {
                $this->storedConfig[$option->getName()] = $option->getValue();
            }
        }
    }

}