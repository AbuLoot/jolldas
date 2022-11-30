<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    protected $table = 'categories';

    protected $fillable = [
        'sort_id',
        'slug',
        'title',
        'title_extra',
        'image',
        'meta_title',
        'meta_description',
        'lang',
        '_lft',
        '_rgt',
        'parent_id',
        'status',
    ];

    public function companies()
    {
        return $this->hasMany('App\Models\Company');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }

    public function discounts()
    {
        return $this->hasMany('App\Models\Discount');
    }
}