<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Kalnoy\Nestedset\NodeTrait;

class Project extends Model
{
    use NodeTrait;

    protected $table = 'projects';

    public function companies()
    {
        return $this->hasMany('App\Models\Company');
    }

    public function products()
    {
        // return $this->hasMany('App\Models\Product');
        return $this->belongsToMany('App\Models\Product', 'product_project');
    }
}
