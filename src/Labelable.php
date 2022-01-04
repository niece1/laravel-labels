<?php

namespace Niece1\Labels;

use Niece1\Labels\Models\Label;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @author test
 */
trait Labelable
{
    /**
     * .
     *
     * @return void
     */
    public function labels()
    {
        return $this->morphToMany(Label::class, 'labelable');
    }
    
    public function label($labels)
    {
        $this->addLabels($this->getLabels($labels));
    }
    
    public function unlabel($labels = null)
    {
        if ($labels === null) {
            $this->removeAllLabels();
            return;
        }
        
        $this->removeLabels($this->getLabels($labels));
    }
    
    public function relabel($labels)
    {
        $this->removeAllLabels();
        $this->label($labels);
    }
    
    private function removeLabels(Collection $labels)
    {
        $this->labels()->detach($labels);
        //decrement count
        foreach ($labels->where('count', '>', 0) as $label) {
            $label->decrement('count');
        }
    }
    
    private function removeAllLabels()
    {
        $this->removeLabels($this->labels);
    }
    
    private function addLabels(Collection $labels)
    {
        $sync = $this->labels()->syncWithoutDetaching($labels->pluck('id')->toArray());
        // we use syncWithoutDetaching() instead of attach() because we need following functionality
        foreach (Arr::get($sync, 'attached') as $attachedId) {
            $labels->where('id', $attachedId)->first()->increment('count');
        }
    }
    
    private function getLabels($labels)
    {
        if (is_array($labels)) {
            return $this->getLabelModels($labels);
        }
        
        if ($labels instanceof Model) {
            return $this->getLabelModels([$labels->slug]);
        }
        return $labels;  // filter collection if it comprises not a labels
    }
    
    private function getLabelModels(array $labels)
    {
        return Label::whereIn('slug', $this->rationLabelNames($labels))->get();
    }
    
    private function rationLabelNames(array $labels)
    {
        return array_map(function ($label) {
            return Str::slug($label);
        }, $labels);
    }
    
    private function filterLabelsCollection(Collection $labels)
    {
        $labels->filter(function ($label) {
            return $label instanceof Model;
        });
    }
    
    public function scopeWithAnyLabel($query, array $labels)
    {
        return $query->hasLabels($labels);
    }
    
    public function scopeWithAllLabels($query, array $labels)
    {
        foreach ($labels as $label) {
            $query->hasLabels([$label]);
        }
        return $query;
    }
    
    public function scopeHasLabels($query, array $labels)
    {
        return $query->whereHas('labels', function ($query) use ($labels) {
            return $query->whereIn('slug', $labels);
        });
    }
}
