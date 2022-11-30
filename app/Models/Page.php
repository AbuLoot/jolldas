<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Kalnoy\Nestedset\NodeTrait;

class Page extends Model
{
	use NodeTrait;

    protected $table = 'pages';
}
