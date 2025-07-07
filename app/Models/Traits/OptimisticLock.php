<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait OptimisticLock {
    public function optimisticUpdate(array $values = []): bool {
        $primaryKeyColumn = $this->primaryKey;
        $updatedAtColumn = static::UPDATED_AT;
        $updated = static::query()->where($primaryKeyColumn, $this->$primaryKeyColumn)
            ->where($updatedAtColumn, $this->$updatedAtColumn)
            ->update($values);
        return $updated == 1;
    }
}