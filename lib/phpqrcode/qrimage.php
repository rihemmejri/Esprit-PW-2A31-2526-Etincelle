<?php
/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
 
    define('QR_IMAGE', true);

    class QRimage {
    
        //----------------------------------------------------------------------
        public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE) 
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame);
            
            if ($filename === false) {
                Header("Content-type: image/png");
                imagepng($image);
            } else {
                if($saveandprint===TRUE){
                    imagepng($image, $filename);
                    header("Content-type: image/png");
                    imagepng($image);
                }else{
                    imagepng($image, $filename);
                }
            }
            
            imagedestroy($image);
        }
    
        //----------------------------------------------------------------------
        public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85) 
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame);
            
            if ($filename === false) {
                Header("Content-type: image/jpeg");
                imagejpeg($image, null, $q);
            } else {
                imagejpeg($image, $filename, $q);            
            }
            
            imagedestroy($image);
        }
    
        //----------------------------------------------------------------------
        private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4) 
        {
            $h = count($frame);
            $w = strlen($frame[0]);
            
            $imgW = $w + 2*$outerFrame;
            $imgH = $h + 2*$outerFrame;
            
            // FIX: Remplacer ImageCreate() par imagecreatetruecolor() pour PHP 8+
            if (function_exists('imagecreatetruecolor')) {
                $base_image = imagecreatetruecolor($imgW, $imgH);
            } else {
                $base_image = imagecreate($imgW, $imgH);
            }
            
            // Vérifier si l'image a été créée avec succès
            if (!$base_image) {
                throw new Exception("Impossible de créer l'image GD. Vérifiez que l'extension GD est installée et activée.");
            }
            
            // Activer l'anti-aliasing si disponible
            if (function_exists('imageantialias')) {
                imageantialias($base_image, true);
            }
            
            $col[0] = imagecolorallocate($base_image,255,255,255);
            $col[1] = imagecolorallocate($base_image,0,0,0);
            
            // Remplir le fond en blanc
            imagefilledrectangle($base_image, 0, 0, $imgW - 1, $imgH - 1, $col[0]);
            
            // Dessiner les pixels noirs
            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        // Utiliser imagefilledrectangle au lieu de imagesetpixel pour une meilleure qualité
                        imagefilledrectangle($base_image, 
                            $x+$outerFrame, 
                            $y+$outerFrame, 
                            $x+$outerFrame, 
                            $y+$outerFrame, 
                            $col[1]
                        );
                    }
                }
            }
            
            // Créer l'image redimensionnée
            $target_image = imagecreatetruecolor($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            
            // Activer l'anti-aliasing sur l'image cible
            if (function_exists('imageantialias')) {
                imageantialias($target_image, true);
            }
            
            // Redimensionner avec une meilleure qualité
            imagecopyresampled($target_image, $base_image, 0, 0, 0, 0, 
                $imgW * $pixelPerPoint, 
                $imgH * $pixelPerPoint, 
                $imgW, 
                $imgH
            );
            
            imagedestroy($base_image);
            
            return $target_image;
        }
    }
?>