<?php

namespace Pepeverde;

use Exception;
use FilesystemIterator;
use Imagecow\Image;
use Imagecow\ImageException;
use ImagickException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

class FileSystem
{
    /**
     * @param string $dir
     */
    public static function isDirEmpty($dir): bool
    {
        $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);

        return 0 === iterator_count($di);
    }

    /**
     * @param string $name
     * @param int    $retinaType
     */
    public static function getRetinaName($name, $retinaType = 2): ?string
    {
        if (!empty($name)) {
            $v = pathinfo($name);

            return $v['filename'] . '@' . $retinaType . 'x.' . $v['extension'];
        }

        return null;
    }

    /**
     * @param string $path
     *
     * @return bool|string
     */
    public static function getFileTypeFromPath($path)
    {
        if (!is_file($path)) {
            return false;
        }

        return self::getFileTypeFromMime(mime_content_type($path));
    }

    /**
     * @param string $mime
     */
    private static function getFileTypeFromMime($mime): string
    {
        switch ($mime) {
            case 'application/pdf':
                return 'pdf';
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
            case 'application/vnd.ms-excel':
                return 'excel';
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.template':
            case 'application/msword':
                return 'word';
            case 'application/zip':
            case 'application/x-rar-compressed':
            case 'application/x-7z-compressed':
                return 'archive';
            case 'image/jpeg':
            case 'image/png':
                return 'image';
            default:
                return 'file';
        }
    }

    /**
     * @param int|float|string|null $size
     * @param int                   $precision
     */
    public static function formatSize($size, $precision = 2): string
    {
        if (!is_numeric($size)) {
            return '0';
        }

        if (0 === $size || '0' === $size) {
            return '0';
        }

        $size = (float)$size;

        $base = log($size) / log(1024);
        $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];

        return round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }

    public static function deleteDir($path): void
    {
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    self::deleteFile($file->getPathname());
                }
            }

            rmdir($path);
        } else {
            self::deleteFile($path);
        }
    }

    public static function deleteFile($path_to_file): void
    {
        if (is_file($path_to_file)) {
            unlink($path_to_file);
        }
    }

    public static function checkImageDimensions(array $uploaded_dimensions, array $required_image_dimensions): bool
    {
        if (!isset($uploaded_dimensions['width'], $uploaded_dimensions['height'], $required_image_dimensions['width'], $required_image_dimensions['height'])) {
            throw new RuntimeException('Dimensions must be set to be checked');
        }

        return ($uploaded_dimensions['width'] >= $required_image_dimensions['width']) && ($uploaded_dimensions['height'] >= $required_image_dimensions['height']);
    }

    public static function checkFixedImageDimensions(array $uploaded_dimensions, array $required_image_dimensions): bool
    {
        if (!isset($uploaded_dimensions['width'], $uploaded_dimensions['height'], $required_image_dimensions['width'], $required_image_dimensions['height'])) {
            throw new RuntimeException('Dimensions must be set to be checked');
        }

        return ((int)$uploaded_dimensions['width'] === (int)$required_image_dimensions['width']) && ((int)$uploaded_dimensions['height'] === (int)$required_image_dimensions['height']);
    }

    /**
     * @param string $image_type
     */
    public static function checkIfIsImage($image_type, array $allowed_types): bool
    {
        if (in_array($image_type, $allowed_types, true)) {
            return true;
        }

        return false;
    }

    /**
     * @param Image $image
     * @param array $uploaded_dimensions
     */
    public static function setDimensionsVariables(&$image, $uploaded_dimensions): array
    {
        $uploaded_check = [
            'width' => 0,
            'height' => 0,
        ];
        if (true === $uploaded_dimensions['width']) {
            $uploaded_check['width'] = $image->getWidth();
        }

        if (true === $uploaded_dimensions['height']) {
            $uploaded_check['height'] = $image->getHeight();
        }

        return $uploaded_check;
    }

    /**
     * @param string $path_to_upload
     */
    public static function uploadAndVerifyImage(array $post_files, $path_to_upload, array &$error, array $required_dimensions, array $uploaded_dimensions, array $allowed_types): array
    {
        $image_name = null;
        if (4 !== $post_files['error']) {
            try {
                if ($image_name = Upload::uploadFile($post_files, $path_to_upload, $post_files['name'])) {
                    $image = Image::fromFile($path_to_upload . '/' . $image_name);
                    if (self::checkIfIsImage($image->getMimeType(), $allowed_types)) {
                        //setto le dimensioni richieste per l'immagine
                        $uploaded_check = self::setDimensionsVariables($image, $uploaded_dimensions);
                        if (self::checkImageDimensions($uploaded_check, $required_dimensions)) {
                            return [
                                'result' => true,
                                'image_name' => $image_name,
                            ];
                        }
                        $error[] = 'l\'immagine selezionata non rispetta le dimensioni richieste, file non caricato.';
                    } else {
                        $error[] = 'il file caricato non è un\'immagine, file non caricato.';
                    }
                }
            } catch (ImageException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (ImagickException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (Exception $e) {
                $error[] = $e->getMessage();
                Error::report($e, false);
            }
        }

        return [
            'result' => false,
            'image_name' => $image_name,
            'error' => $error,
        ];
    }

    /**
     * @param string $path_to_upload
     */
    public static function uploadAndVerifyFixedImage(array $post_files, $path_to_upload, array &$error, array $required_dimensions, array $uploaded_dimensions, array $allowed_types): array
    {
        $image_name = null;
        if (4 !== $post_files['error']) {
            try {
                if ($image_name = Upload::uploadFile($post_files, $path_to_upload, $post_files['name'])) {
                    $image = Image::fromFile($path_to_upload . '/' . $image_name);
                    if (self::checkIfIsImage($image->getMimeType(), $allowed_types)) {
                        //setto le dimensioni richieste per l'immagine
                        $uploaded_check = self::setDimensionsVariables($image, $uploaded_dimensions);
                        if (self::checkFixedImageDimensions($uploaded_check, $required_dimensions)) {
                            return [
                                'result' => true,
                                'image_name' => $image_name,
                            ];
                        }
                        $error[] = 'l\'immagine selezionata non rispetta le dimensioni richieste, file non caricato.';
                    } else {
                        $error[] = 'il file caricato non è un\'immagine, file non caricato.';
                    }
                }
            } catch (ImageException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (ImagickException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (Exception $e) {
                $error[] = $e->getMessage();
                Error::report($e, false);
            }
        }

        return [
            'result' => false,
            'image_name' => $image_name,
            'error' => $error,
        ];
    }

    /**
     * @param Image  $image
     * @param array  $cropdata
     * @param string $savepath
     * @param string $image_name
     */
    public static function cropImage($image, $cropdata, $savepath, $image_name): Image
    {
        if (!file_exists($savepath) && !mkdir($savepath, 0777, true) && !is_dir($savepath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $savepath));
        }

        $image->crop($cropdata['width'], $cropdata['height'], $cropdata['x'], $cropdata['y'])
            ->save($savepath . $image_name);

        return $image;
    }
}
