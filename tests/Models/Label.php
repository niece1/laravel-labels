<?php

use Illuminate\Database\Eloquent\Model;
use Niece1\Labels\Labelable;

/**
 * Description of Book
 *
 * @author test
 */
class Label extends Model
{
    use Labelable;
    
    protected $connection = 'sqlite';
    
    public $table = 'labels';
    
    
}
