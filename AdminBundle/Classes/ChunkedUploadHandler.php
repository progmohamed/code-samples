<?php

namespace AdminBundle\Classes;

class ChunkedUploadHandler extends UploadHandler
{

    public $hashedTempFileName;
    public $uniqueFileName;

    public function getHashedTempFileName()
    {
        return $this->hashedTempFileName;
    }

    public function getUniqueFileName()
    {
        return $this->uniqueFileName;
    }

    protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range)
    {
        $ext = strtolower(substr(strrchr($name, '.'), 1));
        $this->hashedTempFileName = 'temp_'.sha1($name).'.'.$ext;
        return $this->hashedTempFileName;
    }

    protected function get_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
        $name = $this->trim_file_name($file_path, $name, $size, $type, $error,
            $index, $content_range);
        $this->uniqueFileName =  $this->get_unique_filename(
            $file_path,
            $this->fix_file_extension($file_path, $name, $size, $type, $error,
                $index, $content_range),
            $size,
            $type,
            $error,
            $index,
            $content_range
        );
        return $this->uniqueFileName;
    }

    public function isLastChunk()
    {
        $is_last_chunk = false;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $is_chunked_upload = !empty($_SERVER['HTTP_CONTENT_RANGE']);
            if ($is_chunked_upload) {
                // [HTTP_CONTENT_RANGE] => bytes 10000000-17679248/17679249 - last chunk looks like this
                if (preg_match('|(\d+)/(\d+)|', $_SERVER['HTTP_CONTENT_RANGE'], $range)) {
                    if ($range[1] == $range[2] - 1) {
                        $is_last_chunk = true;
                    }
                }
            }
        }
        return $is_last_chunk;
    }
}
