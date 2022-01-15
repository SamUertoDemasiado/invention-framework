<?php


namespace OSN\Framework\ORM;


use OSN\Framework\Core\Model;
use OSN\Framework\ORM\Relationships\BelongsTo;
use OSN\Framework\ORM\Relationships\BelongsToMany;
use OSN\Framework\ORM\Relationships\HasMany;
use OSN\Framework\ORM\Relationships\HasOne;

trait ORMBaseTrait
{
    public function hasMany(string $class): HasMany
    {
        /** @var Model $this */
        return new HasMany($this, new $class());
    }

    public function hasOne(string $class): HasOne
    {
        /** @var Model $this */
        return new HasOne($this, new $class());
    }

    public function belongsTo(string $class): BelongsTo
    {
        /** @var Model $this */
        return new BelongsTo($this, new $class());
    }

    public function belongsToMany(string $class, string $helper_table = ''): BelongsToMany
    {
        /** @var Model $this */
        return new BelongsToMany($this, new $class(), $helper_table);
    }
}
