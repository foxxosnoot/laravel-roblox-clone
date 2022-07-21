<?php

class Database
{
    private $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function getUser($id)
    {
        $db = $this->db;
        
        $get = $db->prepare('SELECT * FROM user_avatars WHERE user_id = :id');
        $get->bindValue(':id', $id, PDO::PARAM_INT);
        $get->execute();
        
        if ($get->rowCount() == 0)
            exit('invalid user');

        $user = $get->fetch(PDO::FETCH_OBJ);
        $user->id = $user->user_id;
        $user->hats = [];

        if ($user->hat_1) {
            $item = $this->getItem($user->hat_1);
            $user->hats[] = $item->filename;
        }

        if ($user->hat_2) {
            $item = $this->getItem($user->hat_2);
            $user->hats[] = $item->filename;
        }

        if ($user->hat_3) {
            $item = $this->getItem($user->hat_3);
            $user->hats[] = $item->filename;
        }

        if ($user->face) {
            $item = $this->getItem($user->face);
            $user->face = $item->filename;
        }

        if ($user->gadget) {
            $item = $this->getItem($user->gadget);
            $user->gadget = $item->filename;
        }

        if ($user->tshirt) {
            $item = $this->getItem($user->tshirt);
            $user->tshirt = $item->filename;
        }

        if ($user->shirt) {
            $item = $this->getItem($user->shirt);
            $user->shirt = $item->filename;
        }

        if ($user->pants) {
            $item = $this->getItem($user->pants);
            $user->pants = $item->filename;
        }

        return $user;
    }
    
    public function getItem($id)
    {
        $db = $this->db;
    
        $get = $db->prepare('SELECT id, type, thumbnail_url, filename FROM items WHERE id = :id');
        $get->bindValue(':id', $id, PDO::PARAM_INT);
        $get->execute();
        
        if ($get->rowCount() == 0) {
            exit('invalid item');
        }
        
        $item = $get->fetch(PDO::FETCH_OBJ);
        
        if (in_array($item->type, ['clothing_bundle', 'bundle']))
            $item->bundle_items = $this->getBundleItems($item->id);
        
        return $item;
    }
    
    public function getBundleItems($id)
    {
        $db = $this->db;
    
        $get = $db->prepare('SELECT
            bundle_items.item_id as item_id,
            items.type as item_type,
            items.status as item_status,
            items.filename as item_filename
        FROM bundle_items LEFT JOIN items ON items.id = bundle_items.item_id WHERE bundle_items.bundle_id = :id');
        $get->bindValue(':id', $id, PDO::PARAM_INT);
        $get->execute();
        
        $items = [];
        
        while ($bundle = $get->fetch(PDO::FETCH_OBJ)) {
            $items[] = [
                'type' => $bundle->item_type,
                'filename' => ($bundle->item_status == 'approved') ? $bundle->item_filename : 'denied_item',
            ];
        }
        
        return $items;
    }
}