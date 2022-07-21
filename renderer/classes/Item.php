<?php

class Item
{
    private $item;
    private $blender;
    private $db;
    private $filename;
    
    public function __construct($id, $db, $filename)
    {
        $database = new Database($db);
        
        $this->item = $database->getItem($id);
        $this->blender = new Blender;
        $this->db = $db;
        $this->filename = $filename;
        $this->clothingFilenames = explode(',', $clothingFilenames);
    }
    
    public function render()
    {
        $blender = $this->blender;
        $focused = [];
        
        $focus = config('FOCUS_ITEMS');
        $avatars = config('AVATARS');
        
        if (config('FACES_PNG') && $this->item->type == 'face') {
            return $this->resizeFace($this->filename, $this->item->filename);
        } else if ($this->item->type != 'bundle') {
            $avatar = $avatars[($this->item->type == 'gadget') ? 'GADGET' : 'DEFAULT'];
            $face = ($this->item->type == 'face') ? $this->item->filename : 'default';
        } else {
            $avatar = $avatars['DEFAULT'];
            $face = 'default';
            
            foreach ($this->item->bundle_items as $bundleItem)
                if ($bundleItem['type'] == 'gadget')
                    $avatar = $avatars['GADGET'];
                
                if ($bundleItem['type'] == 'face')
                    $face = $bundleItem['filename'];
        }
        
        $blender->importBlend($avatar);
        $blender->setTexture('face', 'Head', $face);
        
        switch ($this->item->type) {
            case 'hat':
                $blender->importModel('hat', $this->item->filename);
                $focused[] = 'hat';
                
                if ($focus)
                    $focused[] = 'Head';
                else
                    $blender->removeObjects(['Head', 'Torso', 'LeftArm', 'LeftHand', 'RightArm', 'RightHand', 'LeftLeg', 'RightLeg']);
                break;
            case 'face':
                $blender->removeObjects(['Torso', 'LeftArm', 'LeftHand', 'RightArm', 'RightHand', 'LeftLeg', 'RightLeg']);
                $focused[] = 'Head';
                break;
            case 'gadget':
                $blender->importModel('gadget', $this->item->filename);
                $focused[] = 'gadget';
                
                if ($focus) {
                    $focused[] = 'RightArm';
                    $focused[] = 'RightHand';
                } else {
                    $blender->removeObjects(['Head', 'Torso', 'LeftArm', 'LeftHand', 'RightArm', 'RightHand', 'LeftLeg', 'RightLeg']);
                }
                break;
            case 'shirt':
                $blender->setShirt($this->item->filename);
                break;
            case 'pants':
                $blender->setPants($this->item->filename);
                break;
            case 'clothing_bundle':
                $blender->setShirt($this->clothingFilenames[0]);
                $blender->setPants($this->clothingFilenames[1]);
                break;
            case 'crate':
                $blender->importModel('crate', $this->item->filename);
                $blender->removeObjects(['Head', 'Torso', 'LeftArm', 'LeftHand', 'RightArm', 'RightHand', 'LeftLeg', 'RightLeg']);
                $focused[] = 'crate';
                break;
            case 'bundle':
                foreach ($this->item->bundle_items as $bundleItem) {
                    $key = $bundleItem['type'] . generate_filename();
                    
                    if (!in_array($bundleItem['type'], ['face']))
                        $blender->importModel($key, $bundleItem['filename']);
                    
                    if ($bundleItem['type'] == 'head') {
                        $blender->removeObjects(['Head']);
                        $blender->setTexture('face', $key, $face);
                    }
                }
                break;
        }
        
        $blender->colorObjects(color_array('item_body_color'));
        
        if (!in_array($this->item->type, ['shirt', 'pants', 'bundle'])) {
            $blender->rotateCamera($this->item->type == 'face');
            $blender->focus($focused);
        }
        
        $blender->saveThumbnail($this->filename, 'item');
        $blender->execute("item_{$this->item->id}");
    }
    
    public function updateThumbnail()
    {
        delete_thumbnail($this->item->thumbnail_url);
        
        $update = $this->db->prepare('UPDATE items SET thumbnail_url = :thumbnail_url WHERE id = :id');
        $update->bindValue(':id', $this->item->id, PDO::PARAM_INT);
        $update->bindValue(':thumbnail_url', $this->filename, PDO::PARAM_STR);
        $update->execute();
    }
    
    private function resizeFace($filename, $originalFilename)
    {
        $directories = [];
        
        $directories['thumbnails'] = config('DIRECTORIES', 'THUMBNAILS');
        $directories['uploads'] = config('DIRECTORIES', 'UPLOADS');
        $imageSize = config('IMAGE_SIZES', 'ITEM');
        
        $filename = "{$directories['thumbnails']}/{$filename}.png";
        $originalFilename = "{$directories['uploads']}/{$originalFilename}.png";
        
        $image = imagecreatefrompng($originalFilename);
        $newImage = imagecreatetruecolor($imageSize, $imageSize);
        
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        
        $transparency = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        
        imagefilledrectangle($newImage, 0, 0, $imageSize, $imageSize, $transparency);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $imageSize, $imageSize, imagesx($image), imagesy($image));
        
        imagepng($newImage, $filename);
        delete_thumbnail($originalFilename);
    }
}