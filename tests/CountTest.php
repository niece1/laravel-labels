<?php

use Illuminate\Support\Str;

/**
 * Description of CountTest
 *
 * @author test
 */
class CountTest extends TestCase
{
    protected $book;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->book = \Book::create([
            'title' => 'The new title',
        ]);
    }
    
    /** @test */
    public function adding_label_increments_its_count_value()
    {
        $label = \Label::create([
            'title' => 'Manual',
            'slug' => Str::slug('Manual'),
            'count' => 0
        ]);
        
        $this->book->label(['manual']);
        $label = $label->fresh(); // explicitly reassigning variable to make it fresh() to use below
        $this->assertEquals(1, $label->count);
    }
    
    /** @test */
    public function removing_label_decrements_its_count_value()
    {
        $label = \Label::create([
            'title' => 'Manual',
            'slug' => Str::slug('Manual'),
            'count' => 2
        ]);
        
        $this->book->label(['manual']);
        $this->book->unlabel(['manual']);
        $label = $label->fresh();
        $this->assertEquals(2, $label->count);
    }
    
    /** @test */
    public function count_cant_be_less_than_zero()
    {
        $label = \Label::create([
            'title' => 'Manual',
            'slug' => Str::slug('Manual'),
            'count' => 0
        ]);
        
        $this->book->unlabel(['manual']);
        $label = $label->fresh();
        $this->assertEquals(0, $label->count);
    }
    
    /** @test */
    public function count_value_doesnt_incremented_if_already_exists()
    {
        $label = \Label::create([
            'title' => 'Manual',
            'slug' => Str::slug('Manual'),
            'count' => 0
        ]);
        
        $this->book->label(['manual']);
        $this->book->label(['manual']);
        $label = $label->fresh();
        $this->assertEquals(1, $label->count);
    }
}
