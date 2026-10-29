<?php

namespace Files;

use Files\Exception\ConfigurationException;
use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Exception\NotFoundException;
use Files\Exception\NotImplementedException;
use Files\Model\File;

require_once __DIR__ . '/Files.php';

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- PHP defines the stream-wrapper callback names.

/**
 * Opt-in files:// support for native PHP file and directory functions.
 *
 * Register once with StreamWrapper::register(). Both files://folder/file.txt and
 * files:///folder/file.txt identify a path on the configured Files.com site.
 * Credentials belong in SDK configuration or the files.options stream context.
 */
class StreamWrapper
{
    public $context;

    private static $registered = false;
    private $file;
    private $directory;

    public static function register()
    {
        if (self::$registered && in_array('files', stream_get_wrappers(), true)) {
            return true;
        }
        if (in_array('files', stream_get_wrappers(), true)) {
            throw new ConfigurationException('The files stream scheme is already registered');
        }
        self::$registered = stream_wrapper_register('files', __CLASS__, STREAM_IS_URL);
        return self::$registered;
    }

    public static function unregister()
    {
        if (!self::$registered) {
            return false;
        }
        $result = !in_array('files', stream_get_wrappers(), true) || stream_wrapper_unregister('files');
        if ($result) {
            self::$registered = false;
        }
        return $result;
    }

    /** @internal The suffix is a literal native path, never a URL host, query, fragment, or credential. */
    public static function path($uri)
    {
        if (!is_string($uri) || !preg_match('/\Afiles:\/\//i', $uri)) {
            throw new InvalidParameterException('A Files stream path must use the files:// scheme');
        }
        $path = substr($uri, 8);
        if (strpos($path, "\0") !== false) {
            throw new InvalidParameterException('A Files stream path cannot contain a null byte');
        }
        $path = ltrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    /** @internal A rooted spelling keeps the literal name 0 out of generated required-value truthiness checks. */
    public static function apiPath($path)
    {
        return $path === '0' ? '/0' : $path;
    }

    private function settings()
    {
        $context = is_resource($this->context) ? stream_context_get_options($this->context) : [];
        $settings = isset($context['files']) ? $context['files'] : [];
        foreach ($settings as $name => $value) {
            if (!in_array($name, ['options', 'upload_params'], true) || !is_array($value)) {
                throw new InvalidParameterException('Files stream context accepts options and upload_params arrays only');
            }
        }
        return $settings + ['options' => [], 'upload_params' => []];
    }

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        if ($options & STREAM_USE_PATH) {
            throw new NotImplementedException('Files streams do not use the PHP include_path');
        }
        $settings = $this->settings();
        $this->file = new NativeFile(self::path($path), $mode, $settings['options'], $settings['upload_params']);
        $openedPath = $path;
        return true;
    }

    public function stream_read($count)
    {
        return $this->file->read($count);
    }

    public function stream_write($data)
    {
        return $this->file->write($data);
    }

    public function stream_tell()
    {
        return $this->file->tell();
    }

    public function stream_eof()
    {
        return $this->file->eof();
    }

    public function stream_seek($offset, $whence)
    {
        return $this->file->seek($offset, $whence);
    }

    public function stream_stat()
    {
        return $this->file->stat();
    }

    public function stream_flush()
    {
        return $this->file->flush();
    }

    public function stream_close()
    {
        $this->file->close();
        clearstatcache();
    }

    public function url_stat($path, $flags)
    {
        $settings = $this->settings();
        try {
            return self::statForFile(File::find(self::apiPath(self::path($path)), [], $settings['options']));
        } catch (NotFoundException $error) {
            return false;
        }
    }

    /** @internal Native stat has synthetic ownership and permission bits derived from API capabilities. */
    public static function statForFile(File $file)
    {
        if (!in_array($file->getType(), ['file', 'directory'], true) || ($file->getType() === 'file' && (!is_int($file->getSize()) || $file->getSize() < 0))) {
            throw new ApiBadResponseException('The Files metadata has no usable file type or size');
        }
        $directory = $file->getType() === 'directory';
        $permissions = (string) $file->getPermissions();
        $mode = $directory ? 0040000 : 0100000;
        if (strpos($permissions, 'r') !== false || ($directory && strpos($permissions, 'l') !== false)) {
            $mode |= 0444;
        }
        if (strpos($permissions, 'w') !== false) {
            $mode |= 0222;
        }
        if ($directory && strpos($permissions, 'l') !== false) {
            $mode |= 0111;
        }
        $mtime = $file->getProvidedMtime() ?: $file->getMtime();
        $mtime = $mtime ? strtotime($mtime) : 0;
        $ctime = $file->getCreatedAt() ? strtotime($file->getCreatedAt()) : 0;
        $values = [0, 0, $mode, 1, 0, 0, 0, $directory ? 0 : $file->getSize(), 0, $mtime ?: 0, $ctime ?: 0, -1, -1];
        return array_merge($values, array_combine(['dev', 'ino', 'mode', 'nlink', 'uid', 'gid', 'rdev', 'size', 'atime', 'mtime', 'ctime', 'blksize', 'blocks'], $values));
    }

    public function unlink($path)
    {
        throw new NotImplementedException('Files streams do not provide type-conditional native unlink; use the File delete API');
    }

    public function dir_opendir($path, $options)
    {
        $settings = $this->settings();
        $this->directory = new Stream\DirectoryHandle(self::path($path), $settings['options']);
        return true;
    }

    public function dir_readdir()
    {
        return $this->directory->read();
    }

    public function dir_rewinddir()
    {
        return $this->directory->rewind();
    }

    public function dir_closedir()
    {
        $result = $this->directory->close();
        $this->directory = null;
        return $result;
    }

    public function mkdir($path, $mode, $options)
    {
        $settings = $this->settings();
        return Stream\DirectoryHandle::create(self::path($path), ($options & STREAM_MKDIR_RECURSIVE) !== 0, $mode, $settings['options']);
    }

    public function rmdir($path, $options)
    {
        $settings = $this->settings();
        return Stream\DirectoryHandle::remove(self::path($path), $settings['options']);
    }

    public function rename($from, $to)
    {
        throw new NotImplementedException('Files streams do not provide synchronous native rename; use the File move API');
    }

    public function stream_lock($operation)
    {
        throw new NotImplementedException('Files streams do not provide native file locks');
    }

    public function stream_truncate($size)
    {
        throw new NotImplementedException('Files streams do not provide native truncate');
    }

    public function stream_metadata($path, $option, $value)
    {
        throw new NotImplementedException('Files streams do not provide native touch, chmod, or ownership changes');
    }

    public function stream_cast($castAs)
    {
        throw new NotImplementedException('Files streams do not expose a local file descriptor');
    }

    public function stream_set_option($option, $arg1, $arg2)
    {
        if (($option === STREAM_OPTION_READ_BUFFER || $option === STREAM_OPTION_WRITE_BUFFER) && $arg1 === STREAM_BUFFER_NONE) {
            return true;
        }
        throw new NotImplementedException('Files streams use blocking SDK transport and bounded internal buffers');
    }
}
