<?php

namespace CommonBundle\Lib;

class ImageConverter
{

    public function imageStringToJPEG($imageString)
    {
        try{
            $im = imagecreatefromstring($imageString);
            if($im !== false) {
                ob_start();
                imagejpeg($im);
                $jpg = ob_get_contents();
                ob_end_clean();
                return $jpg;
            }else{
                return false;
            }
        }catch(\Exception $e) {
            return false;
        }
    }

    public function generateThumbnailFromJPGString($jpgString, $maxWidth = 150, $maxHeight = 150, $strict = false)
    {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesizefromstring($jpgString);
        $source_gd_image = imagecreatefromstring($jpgString);
        if ($source_gd_image === false) {
            return false;
        }
        if($strict) {
            $thumbnail_image_width = $maxWidth;
            $thumbnail_image_height = $maxHeight;
        }else {
            $source_aspect_ratio = $source_image_width / $source_image_height;
            $thumbnail_aspect_ratio = $maxWidth / $maxHeight;
            if ($source_image_width <= $maxWidth && $source_image_height <= $maxHeight) {
                $thumbnail_image_width = $source_image_width;
                $thumbnail_image_height = $source_image_height;
            } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                $thumbnail_image_width = (int)($maxHeight * $source_aspect_ratio);
                $thumbnail_image_height = $maxHeight;
            } else {
                $thumbnail_image_width = $maxWidth;
                $thumbnail_image_height = (int)($maxWidth / $source_aspect_ratio);
            }
        }
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
        ob_start();
        imagejpeg($thumbnail_gd_image, null, 100);
        $jpg = ob_get_contents();
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        ob_end_clean();
        return $jpg;
    }
}