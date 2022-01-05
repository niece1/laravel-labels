<?php

use Illuminate\Support\Str;

/**
 * Description of LabelsStringUsageTest
 *
 * @author test
 */
class StringUsageTest extends TestCase
{
    protected $book;
    
    public function setUp(): void
    {
        parent::setUp();
        
        foreach (['Manual', 'Detective', 'Fantasy', 'Nature', 'Indexing Mysql'] as $label) {
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
    public function adding_label_to_the_model()
    {
        $this->book->label(['detective', 'fantasy']);
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Detective', 'Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function removing_label_from_the_model()
    {
        $this->book->label(['detective', 'fantasy', 'nature']);
        $this->book->unlabel(['fantasy']);
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Detective', 'Nature'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function removing_all_labels_from_the_model()
    {
        $this->book->label(['detective', 'fantasy', 'nature']);
        $this->book->unlabel();
        $this->book->load('labels');
        $this->assertCount(0, $this->book->labels);
        $this->assertEquals(0, $this->book->labels->count());
    }
    
    /** @test */
    public function relabeling_all_labels()
    {
        $this->book->label(['detective', 'fantasy', 'nature']);
        $this->book->relabel(['manual', 'fantasy']);
        $this->book->load('labels');
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Manual', 'Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function non_existing_labels_ignored()
    {
        $this->book->label(['detective', 'fantasy', 'engineering']);
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Detective', 'Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function ration_ambivalent_labels()
    {
        $this->book->label(['Detective', 'fAntasy', 'Indexing Mysql']);
        $this->assertCount(3, $this->book->labels);
        
        foreach (['Detective', 'Fantasy', 'Indexing Mysql'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
}
