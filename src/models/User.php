<?php
namespace SkeletonAPI\Models;

use Illuminate\Database\Eloquent\Model;
use SkeletonAPI\lib\UtilTrait;
use SkeletonAPI\lib\Validator;

class User extends Model
{
    use UtilTrait;

    /**
     * @var array  Validation rules
     */
    public static $rules = [
        'email' => 'StringType|Email',
        'password:Password' => 'StringType|Length(10,null)',
        'question:Security Question' => 'StringType|Length(8,null)',
        'answer:Security Answer' => 'StringType|Length(8,null)',
    ];
    /**
     * @var string  Name of the table to use within the database
     */
    protected $table = 'users';
    /**
     * @var string  Primary key of the model within the table
     */
    protected $primaryKey = 'uid';
    /**
     * @var array   Attributes that are hidden from the model's array and JSON forms
     */
    protected $hidden = ['password'];
    /**
     * @var array   Attributes that cannot be assigned via mass-assignment
     */
    protected $guarded = ['password', 'updated_at', 'created_at'];

    /**
     * Create a new user by first validating and normalizing the data and then passing it to the Eloquent model for
     * creation
     * @param $attributes
     * @return User
     */
    public static function create(array $attributes)
    {
        // Validate the data
        $validator = new Validator(self::$rules);
        $validator->assert($attributes);

        // Create the user object
        $user = new self();
        $user->email = strtolower($attributes['email']);
        $user->password = password_hash($attributes['password'], PASSWORD_DEFAULT);
        $user->question = $attributes["question"];
        $user->answer = password_hash($attributes['answer'], PASSWORD_DEFAULT);
        $user->activation_key = sha1(mt_rand(10000, 99999) . time() . $attributes['email']);

        // Create the record
        $user->save();
        return $user;
    }
}