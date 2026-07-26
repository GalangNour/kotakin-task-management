<?php

namespace App\Models\Concerns;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasAudit
{
    public static function bootHasAudit(): void
    {
        static::created(fn (Model $model) => $model->recordAudit('created'));
        static::updated(fn (Model $model) => $model->recordAudit('updated'));
        static::deleted(fn (Model $model) => $model->recordAudit('deleted'));

        if (method_exists(static::class, 'restored')) {
            static::restored(fn (Model $model) => $model->recordAudit('restored'));
        }
    }

    public function auditHistory(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable')->latest('created_at');
    }

    protected function recordAudit(string $event): void
    {
        $exclude = property_exists($this, 'auditExclude') ? $this->auditExclude : ['password', 'remember_token'];

        $old = match ($event) {
            'created' => [],
            'updated' => Arr::except(Arr::only($this->getOriginal(), array_keys($this->getChanges())), $exclude),
            default => Arr::except($this->getAttributes(), $exclude),
        };

        $new = match ($event) {
            'deleted' => [],
            'updated' => Arr::except($this->getChanges(), $exclude),
            default => Arr::except($this->getAttributes(), $exclude),
        };

        if ($event === 'updated' && empty($new)) {
            return;
        }

        Audit::create([
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'user_id' => auth()->id(),
        ]);
    }
}
