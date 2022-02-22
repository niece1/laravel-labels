<?php

namespace Niece1\Labels;

use Niece1\Labels\Models\Label;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait Labelable
{
    /**
     * Get all entities' labels.
     */
    public function labels()
    {
        return $this->morphToMany(Label::class, 'labelable');
    }

    /**
     * Label an entity.
     *
     * @param $labels
     * @return void
     */
    public function label($labels)
    {
        $this->addLabels($this->getLabels($labels));
    }

    /**
     * Unlabel an entity.
     *
     * @param $labels
     * @return mixed
     */
    public function unlabel($labels = null)
    {
        if ($labels === null) {
            $this->removeAllLabels();
            return;
        }
        $this->removeLabels($this->getLabels($labels));
    }

    /**
     * Label an entity again.
     *
     * @param $labels
     * @return void
     */
    public function relabel($labels)
    {
        $this->removeAllLabels();
        $this->label($labels);
    }

    /**
     * Remove a label from the entity.
     *
     * @param $labels
     * @return void
     */
    private function removeLabels(Collection $labels)
    {
        $this->labels()->detach($labels);
        //decrement count
        foreach ($labels->where('count', '>', 0) as $label) {
            $label->decrement('count');
        }
    }

    /**
     * Remove all labels from the entity.
     *
     * @return void
     */
    private function removeAllLabels()
    {
        $this->removeLabels($this->labels);
    }

    /**
     * Add a label to the entity.
     *
     * @param $labels
     * @return void
     */
    private function addLabels(Collection $labels)
    {
        $sync = $this->labels()->syncWithoutDetaching($labels->pluck('id')->toArray());
        // we use syncWithoutDetaching() instead of attach() because we need following functionality
        foreach (Arr::get($sync, 'attached') as $attachedId) {
            $labels->where('id', $attachedId)->first()->increment('count');
        }
    }

    /**
     * Get labels.
     *
     * @param $labels
     * @return mixed
     */
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

    /**
     * Get label models.
     *
     * @param array $labels
     * @return array
     */
    private function getLabelModels(array $labels)
    {
        return Label::whereIn('slug', $this->rationLabelNames($labels))->get();
    }

    /**
     * Make label names normalized.
     *
     * @param array $labels
     * @return array
     */
    private function rationLabelNames(array $labels)
    {
        return array_map(function ($label) {
            return Str::slug($label);
        }, $labels);
    }

    /**
     * Filter the collection of labels.
     *
     * @param $labels
     * @return object
     */
    private function filterLabelsCollection(Collection $labels)
    {
        $labels->filter(function ($label) {
            return $label instanceof Model;
        });
    }

    /**
     * Scope a query to include entities with any label.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $labels
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyLabel($query, array $labels)
    {
        return $query->hasLabels($labels);
    }

    /**
     * Scope a query to include entities with all labels.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $labels
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllLabels($query, array $labels)
    {
        foreach ($labels as $label) {
            $query->hasLabels([$label]);
        }

        return $query;
    }

    /**
     * Scope a query to include entities with labels.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $labels
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasLabels($query, array $labels)
    {
        return $query->whereHas('labels', function ($query) use ($labels) {
            return $query->whereIn('slug', $labels);
        });
    }
}
