<?php

namespace Core\Models;

use Core\File\FileInfo;
use Core\Models\Base\File as BaseFile;
use Core\WebFile;
use Core\File\FileInfoInterface;
use Core\File\FileInfoTrait;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Map\TableMap;

class File extends BaseFile implements FileInfoInterface
{
    use FileInfoTrait;

    public static function normalizePath(&$path)
    {
        $path = WebFile::normalizePath($path);
    }

    /**
     * Returns a File object. If the file behind the file's 'path'
     * does not exists in the database, it will be created.
     *
     * @param FileInfoInterface|FileInfoInterface[] $fileInfo
     *
     * @return FileInfoInterface|FileInfoInterface[]
     */
    public static function wrap($fileInfo)
    {
        if (is_array($fileInfo)) {
            $result = [];
            $paths  = [];
            foreach ($fileInfo as $file) {
                if ($file instanceof File) {
                    return $fileInfo; //it's already a `File` array, return it.
                }
                $paths[] = $file->getPath();
            }

            $files = FileQuery::create()
                ->orderById(Criteria::ASC)
                ->filterByPath($paths)
                ->groupByPath()
                ->find()
                ->toKeyIndex('path');

            foreach ($fileInfo as $file) {
                if ($files[$file->getPath()]) {
                    static::checkFileValues($file, $files[$file->getPath()]);
                    $result[] = $files[$file->getPath()];
                } else {
                    $result[] = static::createFromPathInfo($file);
                }
            }
            return $result;
        } else {
            if ($fileInfo instanceof File) {
                return $fileInfo; //it's already a `File`, return it.
            }
            $path = $fileInfo->getPath();
            $fileObj = FileQuery::create()->orderById()->filterByPath($path)->groupByPath()->findOne();
            if (!$fileObj) {
                $fileObj = static::createFromPathInfo($fileInfo);
            } else {
                static::checkFileValues($fileInfo, $fileObj);
            }
            return $fileObj;
        }
    }

    public static function checkFileValues(FileInfo $file, File $databaseFile)
    {
        if ($file->getSize() != $databaseFile->getSize()) {
            $databaseFile->setSize($file->getSize());
        }
        if ($file->getHash() != $databaseFile->getHash()) {
            $databaseFile->setHash($file->getHash());
        }
        if ($file->getType() != $databaseFile->getType()) {
            $databaseFile->setType($file->getType());
        }
        if ($file->getCreatedTime() != $databaseFile->getCreatedTime()) {
            $databaseFile->setCreatedTime($file->getCreatedTime());
        }
        if ($file->getModifiedTime() != $databaseFile->getModifiedTime()) {
            $databaseFile->setModifiedTime($file->getModifiedTime());
        }
    }

    public static function createFromPathInfo(FileInfoInterface $fileInfo)
    {
        $array = $fileInfo->toArray();
        $file = new File();
        $file->fromArray($array, TableMap::TYPE_STUDLYPHPNAME);
        $file->setHash(WebFile::getHash($file->getPath()));
        $file->save();
        return $file;
    }

    public function getCreatedTime()
    {
        return parent::getCreatedTime();
    }

    public function getModifiedTime()
    {
        return parent::getModifiedTime();
    }

    public function toArray(
        $keyType = null,
        $includeLazyLoadColumns = true,
        $alreadyDumpedObjects = array()
    ) {
        $item = parent::toArray(
            null === $keyType ? TableMap::TYPE_STUDLYPHPNAME : $keyType,
            $includeLazyLoadColumns,
            $alreadyDumpedObjects
        );
        $item['name'] = $this->getName();
        $item['dir'] = $this->getDir();
        $item['icon'] = $this->getIcon();
        $item['extension'] = $this->getExtension();
        return $item;
    }

}
