<?php

class Blender
{
    private $isFocused = false;
    private $python = '';
    
    public function __construct()
    {
        $site = config('SITE_NAME');
        $date = date('m/d/Y h:i:s a', time());
        $timezone = date_default_timezone_get();
        
        $this->comment("{$site} Python Renderer\n\nDATE: {$date}\nSERVER TIMEZONE: {$timezone}");
        $this->code('import bpy');
        $this->code('import math');
        $this->code('from mathutils import Euler');
        $this->code('pi = math.pi');
        $this->code('def hex_to_rgb(value):');
        $this->code('gamma = 2.05', 1);
        $this->code('value = value.lstrip("#")', 1);
        $this->code('lv = len(value)', 1);
        $this->code('fin = list(int(value[i:i + lv // 3], 16) for i in range(0, lv, lv // 3))', 1);
        $this->code('r = pow(fin[0] / 255, gamma)', 1);
        $this->code('g = pow(fin[1] / 255, gamma)', 1);
        $this->code('b = pow(fin[2] / 255, gamma)', 1);
        $this->code('fin.clear()', 1);
        $this->code('fin.append(r)', 1);
        $this->code('fin.append(g)', 1);
        $this->code('fin.append(b)', 1);
        $this->code('return tuple(fin)', 1);
    }
    
    public function comment($comment)
    {
        $this->python .= '"""';
        $this->python .= "\n{$comment}\n";
        $this->python .= '"""';
    }
    
    public function code($code, $tabs = 0)
    {
        $tabcode = '';
        
        for ($i = 0; $i < $tabs; $i++)
            $tabcode .= '    ';
        
        $this->python .= "\n\n{$tabcode}{$code}";
    }
    
    public function execute($tmpFilename)
    {
        $tmpDir = sys_get_temp_dir();
        $tmpFile = "{$tmpDir}/{$tmpFilename}.py";
        
        file_put_contents($tmpFile, $this->python);
        exec(escapeshellcmd("blender --background --python {$tmpFile}"));
    }
    
    public function importBlend($filename)
    {
        $this->code("bpy.ops.wm.open_mainfile(filepath='{$filename}')");
    }
    
    public function importModel($var, $filename)
    {
        if ($filename) {
            $dir = config('DIRECTORIES', 'UPLOADS');
            $object = ($var == 'head') ? 'Head' : $var;
            $intensity = ($var == 'head') ? '0.8' : '1.0';
            
            $model = "{$dir}/{$filename}.obj";
            $texture = "{$dir}/{$filename}.png";
            
            if (obj_exists($filename)) {
                $this->code("import_{$var} = bpy.ops.import_scene.obj(filepath='{$model}')");
                $this->code("{$var} = bpy.context.selected_objects[0]");
                $this->code("bpy.context.selected_objects[0].name = '{$object}'");
                
                if (texture_exists($filename)) {
                    $this->code("{$var}Tex = bpy.data.textures.new('ColorTex', type = 'IMAGE')");
                    $this->code("{$var}Tex.image = bpy.data.images.load(filepath='{$texture}')");
                    $this->code("{$var}Mat = bpy.data.materials.new('{$var}Material')");
                    $this->code("{$var}Mat.diffuse_shader = 'LAMBERT'");
                    $this->code("{$var}Mat.diffuse_intensity = {$intensity}");
                    $this->code("{$var}Mat.specular_shader = 'COOKTORR'");
                    $this->code("{$var}Mat.specular_intensity = 1.0");
                    $this->code("{$var}Slot = {$var}Mat.texture_slots.add()");
                    $this->code("{$var}Slot.texture = {$var}Tex");
                    $this->code("{$var}.active_material = {$var}Mat");
                }
                
                if ($var == 'head')
                    $this->removeObjects(['Head']);
            }
        }
    }
    
    public function setTexture($var, $object, $filename)
    {
        $directories = config('DIRECTORIES');
        $texture = ($filename == 'default') ? "{$directories['ROOT']}/img/face.png" : "{$directories['UPLOADS']}/{$filename}.png";
        
        if ($filename == 'default' || texture_exists($filename)) {
            $this->code("if bpy.data.objects.get('{$object}'):");
            $this->code("{$var}Tex = bpy.data.textures.new('ColorTex', type = 'IMAGE')", 1);
            $this->code("{$var}Tex.image = bpy.data.images.load(filepath='{$texture}')", 1);
            $this->code("{$var}Slot = bpy.data.objects['{$object}'].active_material.texture_slots.add()", 1);
            $this->code("{$var}Slot.texture = {$var}Tex", 1);
        }
    }
    
    public function setShirt($filename)
    {
        if ($filename) {
            $this->setTexture('leftArm', 'LeftArm', $filename);
            $this->setTexture('torso', 'Torso', $filename);
            $this->setTexture('rightArm', 'RightArm', $filename);
        }
    }
    
    public function setPants($filename)
    {
        if ($filename) {
            $this->setTexture('leftLeg', 'LeftLeg', $filename);
            $this->setTexture('rightLeg', 'RightLeg', $filename);
        }
    }
    
    public function removeObjects($objects)
    {
        $this->code('objects = bpy.data.objects');
        
        foreach ($objects as $object) {
            $this->code("if bpy.data.objects.get('{$object}'):");
            $this->code("objects.remove(objects['{$object}'], True)", 1);
        }
    }
    
    public function colorObjects($objects)
    {
        foreach ($objects as $object  => $color) {
            $this->code("if bpy.data.objects.get('{$object}'):");
            $this->code("bpy.data.objects['{$object}'].select = True", 1);
            $this->code("bpy.data.objects['{$object}'].active_material.diffuse_color = hex_to_rgb('{$color}')", 1);
        }
    }
    
    public function rotateCamera($isFace = false)
    {
        $amount = (!$isFace) ? 0.35 : 0.25;
        
        $this->code('camera = bpy.data.objects["Camera"]');
        
        if ($isFace)
            $this->code('camera.rotation_euler[0] = 1.3');
        
        $this->code("camera.rotation_euler[2] = {$amount}");
        $this->code('camera.rotation_mode = "XYZ"');
    }
    
    public function focus($objects)
    {
        $this->isFocused = true;
        $this->code('for object in bpy.context.scene.objects:');
        $this->code('object.select = False', 1);
        
        foreach ($objects as $object) {
            $this->code("if bpy.data.objects.get('{$object}'):");
            $this->code("bpy.data.objects['{$object}'].select = True", 1);
        }
        
        $this->code('bpy.ops.view3d.camera_to_view_selected()');
    }
    
    public function saveThumbnail($filename, $imageSize)
    {
        $imageSize = config('IMAGE_SIZES', strtoupper($imageSize)) ?? 512;
        $directory = config('DIRECTORIES', 'THUMBNAILS');
        $filename = "{$directory}/{$filename}.png";
        
        if (!$this->isFocused) {
            $this->code('for object in bpy.context.scene.objects:');
            $this->code('if object.type == "MESH":', 1);
            $this->code('object.select = True', 2);
            $this->code('bpy.context.scene.objects.active = object', 2);
            $this->code('else:', 1);
            $this->code('object.select = False', 2);
            $this->code('bpy.ops.object.join()');
            $this->code('bpy.ops.view3d.camera_to_view_selected()');
        }
        
        $this->code('origAlphaMode = bpy.data.scenes["Scene"].render.alpha_mode');
        $this->code('bpy.data.scenes["Scene"].render.alpha_mode = "TRANSPARENT"');
        $this->code('bpy.data.scenes["Scene"].render.alpha_mode = origAlphaMode');
        $this->code("bpy.data.scenes['Scene'].render.resolution_x = {$imageSize}");
        $this->code("bpy.data.scenes['Scene'].render.resolution_y = {$imageSize}");
        $this->code("bpy.data.scenes['Scene'].render.filepath = '{$filename}'");
        $this->code('bpy.ops.render.render(write_still=True)');
    }
    
    public function saveHeadshot($filename)
    {
        $camera = config('HEADSHOT_CAMERA');
        $directory = config('DIRECTORIES', 'THUMBNAILS');
        $imageSize = config('IMAGE_SIZES', 'USER_HEADSHOT');
        $filename = "{$directory}/{$filename}_headshot.png";
        
        $this->code('camera = bpy.data.objects["Camera"]');
        $this->code("camera.location.x = {$camera['LOCATION']['X']}");
        $this->code("camera.location.y = {$camera['LOCATION']['Y']}");
        $this->code("camera.location.z = {$camera['LOCATION']['Z']}");
        $this->code("camera.rotation_euler[0] = {$camera['ROTATION']['X']} * (pi / 180.0)");
        $this->code("camera.rotation_euler[1] = {$camera['ROTATION']['Y']} * (pi / 180.0)");
        $this->code("camera.rotation_euler[2] = {$camera['ROTATION']['Z']} * (pi / 180.0)");
        $this->code('camera.rotation_mode = "XYZ"');
        $this->code("bpy.data.scenes['Scene'].render.resolution_x = {$imageSize}");
        $this->code("bpy.data.scenes['Scene'].render.resolution_y = {$imageSize}");
        $this->code("bpy.data.scenes['Scene'].render.filepath = '{$filename}'");
        $this->code('bpy.ops.render.render(write_still=True)');
    }
}