<?php

namespace FileBundle\Service\FileService;

use CommonBundle\Classes\PublicService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileService extends PublicService
{
    const SECURE_ROOT_UPLOAD_DIR = 'files';
    const ROOT_UPLOAD_DIR = 'web/files';

    public function getName()
    {
        return 'الملفات';
    }

    public function uploadFile(UploadedFile $uploadedFile, $serviceBaseFolder)
    {
        return $this->upload($uploadedFile, $serviceBaseFolder);
    }

    public function uploadFileSecure(UploadedFile $uploadedFile, $serviceBaseFolder)
    {
        return $this->upload($uploadedFile, $serviceBaseFolder, true);
    }

    public function uploadFileByCopy($sourceFile, $serviceBaseFolder)
    {
        return $this->upload($sourceFile, $serviceBaseFolder);
    }

    public function uploadFileSecureByCopy($sourceFile, $serviceBaseFolder)
    {
        return $this->upload($sourceFile, $serviceBaseFolder, true);
    }

    public function uploadFileToSecureTempFolder(UploadedFile $uploadedFile)
    {
        if ($this->getAbsoluteSecureFolder() === false) {
            throw new \Exception($this->getAbsoluteSecureFolder() . ' folder does not exist');
        }

        $baseTempFolderName = sha1(uniqid());
        $fullDirPath = $this->getAbsoluteSecureFolder() . '/temp/' . $baseTempFolderName;
        mkdir($fullDirPath, 0777, true);

        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $baseFileName = sha1(uniqid()) . '.' . $ext;
        $fullFilePath = $uploadedFile->move($fullDirPath, $baseFileName);

        return [
            'baseTempFolderName' => $baseTempFolderName,
            'baseFileName' => $baseFileName,
            'ext' => $ext,
            'fullDirPath' => $fullDirPath,
            'fullFilePath' => $fullFilePath
        ];
    }


    public function removeFolder($fullDirPath)
    {
        if (file_exists($fullDirPath) === false) {
            throw new \Exception($fullDirPath . ' folder does not exist');
        }
        $fullPath = $fullDirPath;
        $this->delTree($fullPath);
    }

    public function removeFile($fileFullRelativePath)
    {
        $kernerRootDir = $this->container->getParameter('kernel.root_dir');
        $webFolder = $this->getRealPath($kernerRootDir . '/../web');

        $fileFullRelativePath = trim($fileFullRelativePath);
        if ($fileFullRelativePath != '') {
            if (substr($fileFullRelativePath, 0, 1) != '/') {
                $fileFullRelativePath = '/' . $fileFullRelativePath;
            }
            $fileFullAbsolutePath = $webFolder . $fileFullRelativePath;

            if (file_exists($fileFullAbsolutePath)) {
                unlink($fileFullAbsolutePath);
                return true;
            }
        }
        return false;
    }

    public function removeFileSecure($fileFullRelativePath)
    {
        $fileFullRelativePath = trim($fileFullRelativePath);
        if ($fileFullRelativePath != '') {
            if (substr($fileFullRelativePath, 0, 1) != '/') {
                $fileFullRelativePath = '/' . $fileFullRelativePath;
            }
            $fileFullAbsolutePath = $this->getAbsoluteSecureFolder() . $fileFullRelativePath;

            if (file_exists($fileFullAbsolutePath)) {
                unlink($fileFullAbsolutePath);
                return true;
            }
        }
        return false;
    }

    public function forceDownloadSecureFile($fileFullRelativePath, $fileName = null)
    {
        $fileFullRelativePath = trim($fileFullRelativePath);
        if ($fileFullRelativePath != '') {
            if (substr($fileFullRelativePath, 0, 1) != '/') {
                $fileFullRelativePath = '/' . $fileFullRelativePath;
            }
            $fileFullAbsolutePath = $this->getAbsoluteSecureFolder() . $fileFullRelativePath;

            if (file_exists($fileFullAbsolutePath)) {
                if ($fileName) {
                    $ext = pathinfo($fileFullAbsolutePath, PATHINFO_EXTENSION);
                    $fileName = $fileName . '.' . $ext;
                }
                $response = new BinaryFileResponse($fileFullAbsolutePath);
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $fileName ? $fileName : ''
                );
                return $response;
            }
        }
        return false;
    }

    public function forceDownloadFile($fileFullRelativePath, $fileName = null)
    {
        $fileFullRelativePath = trim($fileFullRelativePath);
        if ($fileFullRelativePath != '') {
            if (substr($fileFullRelativePath, 0, 1) != '/') {
                $fileFullRelativePath = '/' . $fileFullRelativePath;
            }
            $fileFullAbsolutePath = $this->getAbsoluteFolder().'/../' . $fileFullRelativePath;

            if (file_exists($fileFullAbsolutePath)) {
                if ($fileName) {
                    $ext = pathinfo($fileFullAbsolutePath, PATHINFO_EXTENSION);
                    $fileName = $fileName . '.' . $ext;
                }
                $response = new BinaryFileResponse($fileFullAbsolutePath);
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    $fileName ? $fileName : ''
                );
                return $response;
            }
        }
        return false;
    }

    private function upload($uploadedFile, $serviceBaseFolder, $secure = false)
    {
        $out = [];
        $serviceBaseFolder = $this->cleanupPathName($serviceBaseFolder);
        if (!$serviceBaseFolder) {
            throw new \Exception('Service base folder is empty');
        }

        $kernerRootDir = $this->container->getParameter('kernel.root_dir');
        if ($secure) {
            $rootFolder = $this->getAbsoluteSecureFolder();
            $absoluteSecureFolder = $this->getRealPath($kernerRootDir . '/../' . self::SECURE_ROOT_UPLOAD_DIR);
        } else {
            $rootFolder = $this->getAbsoluteFolder();
            $absoluteWebFolder = $this->getRealPath($kernerRootDir . '/../web');
        }

        if ($rootFolder === false) {
            throw new \Exception($rootFolder . ' folder does not exist');
        }
        $serviceRootFolder = $rootFolder . '/' . $serviceBaseFolder;
        $fullDirPath = $this->createUploadSubFolder($serviceRootFolder);

        $out['fullDirPath'] = $fullDirPath;

        if ($uploadedFile instanceof UploadedFile) {
            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            $baseFileName = sha1(uniqid()) . '.' . $ext;
            $fullFilePath = $uploadedFile->move($fullDirPath, $baseFileName);
            $out['baseFileName'] = $baseFileName;
            $out['ext'] = $ext;
            $out['fullFilePath'] = $this->cleanupPathName($fullFilePath->getPathname());
        } else {
            $ext = pathinfo($uploadedFile, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $baseFileName = sha1(uniqid()) . '.' . $ext;
            $target = $fullDirPath . '/' . $baseFileName;

            $this->copyFile($uploadedFile, $target);
            $out['baseFileName'] = $baseFileName;
            $out['ext'] = $ext;
            $out['fullFilePath'] = $target;
        }
        if ($secure) {
            $out['relativeFullFilePath'] = substr($out['fullFilePath'], strlen($absoluteSecureFolder) + 1);
        } else {
            $out['relativeFullFilePath'] = substr($out['fullFilePath'], strlen($absoluteWebFolder) + 1);
        }
        return $out;
    }

    public function getAbsoluteSecureFolder()
    {
        $rootFolder = $this->container->getParameter('kernel.root_dir') . '/../' . self::SECURE_ROOT_UPLOAD_DIR;
        return $this->getRealPath($rootFolder);
    }

    public function getAbsoluteFolder()
    {
        $rootFolder = $this->container->getParameter('kernel.root_dir') . '/../' . self::ROOT_UPLOAD_DIR;
        return $this->getRealPath($rootFolder);
    }

    private function copyFile($source, $target)
    {
        $hsource = fopen($source, "rb");
        $htarget = fopen($target, "wb");
        while (!feof($hsource)) {
            $contents = fread($hsource, 1024);
            fwrite($htarget, $contents);
        }
        fclose($hsource);
        fclose($htarget);
    }

    public function cleanupPathName($path)
    {
        $str = str_replace("\\", '/', $path);
        $str = trim($str);
        $str = rtrim($str, "/");
        return $str;
    }

    public function createUploadSubFolder($serviceRootFolder)
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
//        $hour = date("h");
//        $minute = date("i");

        $directory = $year . '/' . $month . '/' . $day;
//        $directory = $year . '/' . $month . '/' . $day . '/' . $hour . '/' . $minute;
        $fullPath = $serviceRootFolder . '/' . $directory;
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
        return $fullPath;
    }

    private function delTree($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function getRealPath($path)
    {
        $realpath = realpath($path);
        if (false !== $realpath) {
            return str_replace("\\", '/', $realpath);
        }
        return false;
    }
}