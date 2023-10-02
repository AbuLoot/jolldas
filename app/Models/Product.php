<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    protected $table = 'products';

    public $asYouType = true;

    protected $fillable = [
        'sort_id',
        'category_id',
        'user_id',
        'company_id',
        'slug',
        'title',
        'meta_title',
        'meta_description',
        'barcodes',
        'id_codes',
        'purchase_price',
        'wholesale_price',
        'price',
        'count_in_stores',
        'count',
        'count_web',
        'unit',
        'type',
        'description',
        'characteristic',
        'parameters',
        'path',
        'image',
        'images',
        'lang',
        'views',
        'status'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        // $array = $this->toArray();

        // Customize array...
        $array = [
            'id' => $this->id,
            'title' => $this->title,
            'barcodes' => $this->barcodes,
            'id_codes' => $this->id_codes,
            'description' => $this->description,
            'characteristic' => $this->characteristic
        ];

        return $array;
    }

    public function category()
    {
    	return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'product_project', 'product_id', 'project_id');
    }

    public function modes()
    {
        return $this->belongsToMany('App\Models\Mode', 'product_mode', 'product_id', 'mode_id');
    }

    public function options()
    {
        return $this->belongsToMany('App\Models\Option', 'product_option', 'product_id', 'option_id');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'product_order', 'product_id', 'order_id');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'parent');
    }
}
