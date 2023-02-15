<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    /**
     * setup variable mass assignment.
     *
     * @var array
     */
    protected $table = 'homepage';
    protected $primaryKey = 'id';
    protected $fillable = [
        'key',
        'label',
        'value',
    ];
}
