<?php

namespace Pepeverde;

use Imagecow\Image;
use Imagecow\ImageException;

class FileSystem
{
    public static function isDirEmpty(string $dir): bool
    {
        $di = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);

        return 0 === iterator_count($di);
    }

    public static function getRetinaName(string $name, int $retinaType = 2): ?string
    {
        if (!empty($name)) {
            $v = pathinfo($name);

            return $v['filename'] . '@' . $retinaType . 'x.' . $v['extension'];
        }

        return null;
    }

    public static function getFileTypeFromPath(string $path): bool|string
    {
        if (!is_file($path)) {
            return false;
        }

        return self::getFileTypeFromMime(mime_content_type($path));
    }

    private static function getFileTypeFromMime(string $mime): string
    {
        return match ($mime) {
            'application/pdf' => 'pdf',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'application/vnd.ms-excel' => 'excel',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', 'application/msword' => 'word',
            'application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed' => 'archive',
            'image/jpeg', 'image/png' => 'image',
            default => 'file',
        };
    }

    public static function formatSize(float|int|string|null $size, int $precision = 2): string
    {
        if (!is_numeric($size)) {
            return '0.00';
        }

        if (0 === $size || '0' === $size) {
            return '0.00';
        }

        $size = (float)$size;

        $base = log($size) / log(1024);
        $suffixes = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];

        return number_format(1024 ** ($base - floor($base)), $precision) . $suffixes[(int)floor($base)];
    }

    public static function deleteDir(string $path): void
    {
        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
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

    public static function deleteFile(string $path_to_file): void
    {
        if (is_file($path_to_file)) {
            unlink($path_to_file);
        }
    }

    /**
     * @param array{width: int, height: int} $uploaded_dimensions
     * @param array{width: int, height: int} $required_image_dimensions
     */
    public static function checkImageDimensions(array $uploaded_dimensions, array $required_image_dimensions): bool
    {
        if (!isset($uploaded_dimensions['width'], $uploaded_dimensions['height'], $required_image_dimensions['width'], $required_image_dimensions['height'])) {
            throw new \RuntimeException('Dimensions must be set to be checked');
        }

        return ($uploaded_dimensions['width'] >= $required_image_dimensions['width']) && ($uploaded_dimensions['height'] >= $required_image_dimensions['height']);
    }

    /**
     * @param array{width: int, height: int} $uploaded_dimensions
     * @param array{width: int, height: int} $required_image_dimensions
     */
    public static function checkFixedImageDimensions(array $uploaded_dimensions, array $required_image_dimensions): bool
    {
        if (!isset($uploaded_dimensions['width'], $uploaded_dimensions['height'], $required_image_dimensions['width'], $required_image_dimensions['height'])) {
            throw new \RuntimeException('Dimensions must be set to be checked');
        }

        return ((int)$uploaded_dimensions['width'] === (int)$required_image_dimensions['width']) && ((int)$uploaded_dimensions['height'] === (int)$required_image_dimensions['height']);
    }

    public static function checkIfIsImage(string $image_type, array $allowed_types): bool
    {
        if (in_array($image_type, $allowed_types, true)) {
            return true;
        }

        return false;
    }

    /**
     * @param array{width: int, height: int} $uploaded_dimensions
     *
     * @return array{width: int, height: int}
     */
    public static function setDimensionsVariables(Image $image, array $uploaded_dimensions): array
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
     * @param array{width: int, height: int} $required_dimensions
     * @param array{width: int, height: int} $uploaded_dimensions
     */
    public static function uploadAndVerifyImage(
        array $post_files,
        string $path_to_upload,
        array &$error,
        array $required_dimensions,
        array $uploaded_dimensions,
        array $allowed_types,
        ?FileMover $fileMover = null,
    ): array {
        $fileMover = $fileMover ?? new FileMover();
        $image_name = null;
        if (\UPLOAD_ERR_NO_FILE !== $post_files['error']) {
            try {
                if ($image_name = Upload::uploadFile($post_files, $path_to_upload, $post_files['name'], $fileMover)) {
                    $image = Image::fromFile($path_to_upload . '/' . $image_name);
                    if (self::checkIfIsImage($image->getMimeType(), $allowed_types)) {
                        // setto le dimensioni richieste per l'immagine
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
                } else {
                    $error[] = 'Upload::uploadFile returned false';
                }
            } catch (ImageException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (\ImagickException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (\Exception $e) {
                $error[] = $e->getMessage();
                Error::report($e, false);
            }
        } else {
            $error[] = 'No file was uploaded';
        }

        return [
            'result' => false,
            'image_name' => $image_name,
            'error' => $error,
        ];
    }

    /**
     * @param array{width: int, height: int} $required_dimensions
     * @param array{width: int, height: int} $uploaded_dimensions
     */
    public static function uploadAndVerifyFixedImage(
        array $post_files,
        string $path_to_upload,
        array &$error,
        array $required_dimensions,
        array $uploaded_dimensions,
        array $allowed_types,
        ?FileMover $fileMover = null,
    ): array {
        $fileMover = $fileMover ?? new FileMover();
        $image_name = null;
        if (4 !== $post_files['error']) {
            try {
                if ($image_name = Upload::uploadFile($post_files, $path_to_upload, $post_files['name'])) {
                    $image = Image::fromFile($path_to_upload . '/' . $image_name);
                    if (self::checkIfIsImage($image->getMimeType(), $allowed_types)) {
                        // setto le dimensioni richieste per l'immagine
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
            } catch (\ImagickException $e) {
                $error[] = 'il file caricato non è un\'immagine valida, file non caricato.';
            } catch (\Exception $e) {
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
     * @param array<string, int> $cropdata
     */
    public static function cropImage(
        Image $image,
        array $cropdata,
        string $savepath,
        string $image_name,
    ): Image {
        if (!file_exists($savepath) && !mkdir($savepath, 0777, true) && !is_dir($savepath)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $savepath));
        }

        $image->crop($cropdata['width'], $cropdata['height'], $cropdata['x'], $cropdata['y'])
            ->save($savepath . $image_name);

        return $image;
    }
}
