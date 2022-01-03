<?php

use Illuminate\Support\Str;

/**
 * Description of LabelsStringUsageTest
 *
 * @author test
 */
class LabelsStringUsageTest extends TestCase
{
    protected $book;
    
    public function setUp(): void
    {
        parent::setUp();
        
        foreach (['Manual', 'Detective', 'Fantasy', 'Nature'] as $label) {
            \Label::create([
            'title' => $label,
            'slug' => Str::slug($label),
            'count' => 0
            ]);
        }
        
        $this->book = \Book::create([
            'title' => 'The new title',
        ]);
    }
    
    /** @test */
    public function able_to_add_label_to_the_model()
    {
        $this->book->label(['detective', 'fantasy']);
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Detective', 'Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
}
