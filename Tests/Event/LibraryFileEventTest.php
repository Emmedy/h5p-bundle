<?php

namespace Emmedy\H5PBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Emmedy\H5PBundle\Event\LibraryFileEvent;

class LibraryFileEventTest extends TestCase
{
    public function testGetFiles()
    {
        // Arrange
        $files = ['file1.js', 'file2.css'];
        $libraryList = ['library1', 'library2'];
        $mode = 'production';

        // Act
        $event = new LibraryFileEvent($files, $libraryList, $mode);

        // Assert
        $this->assertSame($files, $event->getFiles());
    }

    public function testGetLibraryList()
    {
        // Arrange
        $files = ['file1.js', 'file2.css'];
        $libraryList = ['library1', 'library2'];
        $mode = 'production';

        // Act
        $event = new LibraryFileEvent($files, $libraryList, $mode);

        // Assert
        $this->assertSame($libraryList, $event->getLibraryList());
    }

    public function testGetMode()
    {
        // Arrange
        $files = ['file1.js', 'file2.css'];
        $libraryList = ['library1', 'library2'];
        $mode = 'production';

        // Act
        $event = new LibraryFileEvent($files, $libraryList, $mode);

        // Assert
        $this->assertSame($mode, $event->getMode());
    }
}
