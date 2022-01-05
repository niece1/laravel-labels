<?php

use Illuminate\Support\Str;

/**
 * Description of ModelUsageTest
 *
 * @author test
 */
class ModelUsageTest extends TestCase
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
        $this->book->label(\Label::where('slug','detective')->first());
        $this->assertCount(1, $this->book->labels);
        
        $this->assertContains('Detective', $this->book->labels->pluck('title'));
    }
    
    /** @test */
    public function adding_a_collection_of_labels_to_the_model()
    {
        $labels = \Label::whereIn('slug', ['detective', 'fantasy'])->get();
        $this->book->label($labels);
        $this->assertCount(2, $this->book->labels);
        
        foreach (['Detective', 'Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function removing_label_from_the_model()
    {
        $labels = \Label::whereIn('slug', ['detective', 'fantasy'])->get();
        $this->book->label($labels);
        $this->book->unlabel($labels->first());
        $this->assertCount(1, $this->book->labels);
        
        foreach (['Fantasy'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
    
    /** @test */
    public function removing_all_labels_from_the_model()
    {
        $labels = \Label::whereIn('slug', ['detective', 'fantasy'])->get();
        $this->book->label($labels);
        $this->book->unlabel();
        $this->book->load('labels');
        $this->assertCount(0, $this->book->labels);
    }
    
    /** @test */
    public function relabeling_all_models_labels()
    {
        $labels = \Label::whereIn('slug', ['detective', 'fantasy'])->get();
        $relabels = \Label::whereIn('slug', ['detective', 'manual', 'nature'])->get();
        $this->book->label($labels);
        $this->book->relabel($relabels);
        $this->book->load('labels');
        $this->assertCount(3, $this->book->labels);
        
        foreach (['Manual', 'Detective', 'Nature'] as $label) {
            $this->assertContains($label, $this->book->labels->pluck('title'));
        }
    }
}
