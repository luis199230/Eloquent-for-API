<?php

namespace Madeweb\Eloquent\API\Models;

use Madeweb\Eloquent\API\Models\Traits\MicroserviceTrait;
use Illuminate\Contracts\Container\BindingResolutionException;

class BaseModel
{
    use MicroserviceTrait;

    protected $connection;

    protected $relations = [];
    protected $attributes = [];
    protected $fillable = [];


    /**
     * User constructor.
     * @param $api
     * @throws BindingResolutionException
     */
    public function __construct($api)
    {
        $this->connection = app()->make($api);
        $this->setRelationships();
    }

    public function getExcepts()
    {
        return [
            '__construct',
            '__set',
            '__get',
            'fill',
            'setRelationships',
            'getExcepts'
        ];
    }

    public function setRelationships()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method){
            if(!in_array($method, $this->getExcepts())){
                $this->relations[$method] = (object)[];
            }
        }
    }
}
