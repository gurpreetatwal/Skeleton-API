<?php
namespace SkeletonAPI\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * @var string  Name of the table to use within the database
     */
    protected $table = 'users';

    /**
     * @var string  Primary key of the model within the table
     */
    protected $key = 'uid';

    /**
     * @var array   Attributes that are hidden from the model's array and JSON forms
     */
    protected $hidden = ['password'];

    /**
     * @var array   Attributes that cannot be assigned via mass-assignment
     */
    protected $guarded = ['password', 'updated_at', 'created_at'];
}