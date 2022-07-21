<?php

class Preview
{
    private $blender;
    private $type;
    private $filename;
    
    public function __construct($type)
    {
        $this->blender = new Blender;
        $this->type = $type;
        $this->filename = "preview_{$type}";
    }
    
    public function render()
    {
        $this->blender->importBlend(config('AVATARS', 'DEFAULT'));
        $this->blender->setTexture('face', 'Head', ($this->type == 'face') ? 'preview_face' : 'default');
        
        switch ($this->type) {
            case 'shirt':
                $this->blender->setShirt('preview_shirt');
                break;
            case 'pants':
                $this->blender->setPants('preview_pants');
                break;
        }
        
        $this->blender->colorObjects(color_array('item_body_color'));
        $this->blender->saveThumbnail($this->filename, 'user_avatar');
        $this->blender->execute($this->filename);
        
        $base64 = preview_base64($this->type);
        
        delete_thumbnail($this->filename);
        delete_upload($this->filename);
        
        return "data:image/png;base64,{$base64}";
    }
}