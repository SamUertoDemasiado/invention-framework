<?php


namespace OSN\Framework\ORM;


use OSN\Framework\Core\Collection;
use OSN\Framework\Core\Model;

abstract class DualRelationship extends Relationship
{
    protected Model $relationalModel;
    protected Model $baseModel;
    protected string $relationalModelClass;
    protected string $baseModelClass;

    public function __construct(Model $baseModel, Model $relationalModel)
    {
        parent::__construct();
        $this->baseModel = $baseModel;
        $this->relationalModel = $relationalModel;
        $this->baseModelClass = get_class($baseModel);
        $this->relationalModelClass = get_class($relationalModel);
    }

    public function get()
    {
        /** @var Collection $data */
        /** @var Collection $data2 */
        $data = parent::get();

        $data2 = collection();
        $class = get_class($this->relationalModel);

        $data->each(function ($value, $key) use ($class, $data2) {
            $model = new $class;

            foreach ($value as $k => $datum) {
                $model->{$k} = $datum;
            }

            $data2->set($key, $model);
        });

        return $data2;
    }
}
