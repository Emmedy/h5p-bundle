<?php

namespace Emmedy\H5PBundle\Core;


class H5POptions
{
    /**
     * @var array
     */
    private $config;

    private $h5pPath;
    private $folderPath;
    private $kernelRootDir;

    /**
     * H5POptions constructor.
     * @param array $config
     * @param $kernelRootDir
     */
    public function __construct(array $config, $kernelRootDir)
    {
        $this->config = $config;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * Get stored setting.
     *
     * @param string $name
     *   Identifier for the setting
     * @param string $default
     *   Optional default value if settings is not set
     * @return mixed
     *   Whatever has been stored as the setting
     */
    public function getOption($name, $default = NULL) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return $default;
    }

    /**
     * Stores the given setting.
     *
     * @param string $name
     *   Identifier for the setting
     * @param mixed $value Data
     *   Whatever we want to store as the setting
     */
    public function setOption($name, $value)
    {
        $this->config[$name] = $value;

        // Only update the setting if it has infact changed.
//    if ($value !== \Drupal::config('h5p.settings')->get("h5p_{$name}")) {
//      $config = \Drupal::configFactory()->getEditable('h5p.settings');
//      $config->set("h5p_{$name}", $value);
//      $config->save();
//    }
    }

    public function getUploadedH5pFolderPath($set = NULL)
    {
        if (!empty($set)) {
            $this->folderPath = $set;
        }

        return $this->folderPath;
    }

    public function getUploadedH5pPath($set = NULL)
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
        return $this->kernelRootDir . '/..' . $this->getOption('web_path') . $this->getOption('storage_path');
    }

    public function getLibraryFileUrl($libraryFolderName, $fileName)
    {
        return $this->getRelativeH5PPath() . "/libraries/$libraryFolderName/$fileName";
    }

}