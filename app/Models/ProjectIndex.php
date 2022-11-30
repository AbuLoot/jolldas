<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProjectIndex extends Model
{
    use Searchable, HasFactory;

    protected $table = 'projects_index';

    public $timestamps = false;
    public $asYouType = false;

    protected $fillable = [
        'id',
        'sort_id',
        'title',
        'original',
        'lang',
        'status'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        // Customize array...
        $array = [
            'id' => $this->id,
            'title' => $this->title,
            'original' => $this->original
        ];

        return $array;
    }
}
