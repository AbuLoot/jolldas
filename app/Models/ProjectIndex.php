<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ProjectIndex extends Model
{
    use Searchable;

    protected $table = 'projects_index';

    public $timestamps = false;
    public $asYouType = false;

    protected $fillable = [
        'id',
        'sort_id',
        'original',
        'title',
        'lang',
        'status'
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        // Customize array...
        $array = [
            'id' => $this->id,
            'original' => $this->original['original'],
            'title' => $this->title,
        ];

        // dd($array, $this->original, $this->toArray());

        return $array;
    }
}
