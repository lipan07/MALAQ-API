<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public static function getIdByGuardName($guardName)
    {
        $category = self::where('guard_name', $guardName)->first();
        return $category ? $category->id : null;
    }

    public static function getGuardNameById($id)
    {
        $category = self::where('id', $id)->first();
        return $category ? $category->guard_name : null;
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
