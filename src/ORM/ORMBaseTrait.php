<?php


namespace OSN\Framework\ORM;


use OSN\Framework\ORM\Relationships\BelongsTo;
use OSN\Framework\ORM\Relationships\BelongsToMany;
use OSN\Framework\ORM\Relationships\HasMany;
use OSN\Framework\ORM\Relationships\HasOne;

trait ORMBaseTrait
{
    public function hasMany(string $class): HasMany
    {
        return new HasMany($this, new $class());
    }

    public function hasOne(string $class): HasOne
    {
        return new HasOne($this, new $class());
    }

    public function belongsTo(string $class): BelongsTo
    {
        return new BelongsTo($this, new $class());
    }

    public function belongsToMany(string $class, string $helper_table = ''): BelongsToMany
    {
        return new BelongsToMany($this, new $class(), $helper_table);
    }
}
