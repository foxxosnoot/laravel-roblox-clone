<?php

class User
{
    private $user;
    private $blender;
    private $db;
    private $filename;
    
    public function __construct($id, $db, $filename)
    {
        $database = new Database($db);
       
        $this->user = $database->getUser($id);
        $this->blender = new Blender;
        $this->db = $db;
        $this->filename = $filename;
    }
    
    public function render()
    {
        $this->renderAvatar();
        $this->renderHeadshot();
        $this->blender->execute("user_avatar_{$this->user->id}");
    }
    
    public function renderAvatar()
    {
        $this->blender->importBlend(config('AVATARS', ($this->user->gadget) ? 'GADGET' : 'DEFAULT'));
        $this->setAngle();
        $this->generateAvatar();
        $this->blender->importModel('gadget', $this->user->gadget);
        $this->blender->saveThumbnail($this->filename, 'user_avatar');
    }
    
    public function renderHeadshot()
    {
        $this->blender->importBlend(config('AVATARS', 'DEFAULT'));
        $this->generateAvatar();
        $this->blender->saveHeadshot($this->filename);
    }
    
    public function generateAvatar()
    {
        $i = 1;
        
        $this->blender->setTexture('face', 'Head', $this->user->face ?? 'default');
        
        foreach ($this->user->hats as $filename) {
            $this->blender->importModel("hat_{$i}", $filename);
            $i++;
        }
        
        $this->blender->setShirt($this->user->shirt);
        $this->blender->setPants($this->user->pants); 
        $this->blender->colorObjects(color_array(
            $this->user->color_head,
            $this->user->color_torso,
            $this->user->color_left_arm,
            $this->user->color_right_arm,
            $this->user->color_left_leg,
            $this->user->color_right_leg
        ));
    }
    
    public function setAngle()
    {
        switch ($this->user->angle) {
            case 'right':
                $this->blender->rotateCamera();
                break;
        }
    }
    
    public function updateThumbnail()
    {
        delete_thumbnail($this->user->image);
        delete_thumbnail("{$this->user->image}_headshot");
        
        $update = $this->db->prepare('UPDATE user_avatars SET image = :image WHERE user_id = :user_id');
        $update->bindValue(':user_id', $this->user->id, PDO::PARAM_INT);
        $update->bindValue(':image', $this->filename, PDO::PARAM_STR);
        $update->execute();
    }
}