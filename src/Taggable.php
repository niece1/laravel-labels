<?php

namespace Niece1\Credit;

use Niece1\Credit\Models\Tag;

/**
 *
 * @author test
 */
trait Taggable
{
    /**
     * .
     *
     * @return void
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
