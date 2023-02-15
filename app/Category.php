<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{
    /**
     * rules validasi untuk data customers.
     *
     * @var array
     */
    public static $rules = [
        'name'    => 'required'
    ];
    
    /**
     * setup variable mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
    
}
