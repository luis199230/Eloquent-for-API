<?php

namespace Madeweb\Eloquent\API\Models\Traits;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;

trait MicroserviceTrait
{
    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        if (in_array($name, get_class_methods($this))){
            return $this->{$name}();
        }
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        if (isset($this->attributes[$name])) {
            $this->attributes[$name] = $value;
        }
        if (isset($this->relations[$name])){
            return $this->relations[$name] = $value;
        }
    }

    /**
     * @param $data
     */
    private function setAttributes($data)
    {
        foreach ($this->fillable as $attribute) {
            if(isset($data->{$attribute})){
                $this->attributes[$attribute] = $data->{$attribute};
            }
        }
    }

    /**
     * @param $data
     */
    private function setRelations($data)
    {
        foreach ($data as $key => $item) {
            if (array_key_exists($key, $this->relations) && (is_object($item) || is_array($item))) {
                $this->relations[$key] = $data->{$key};
            }
        }
    }

    /**
     * @param $response
     * @return $this
     * @throws Exception
     */
    private function prepareResponse($response)
    {
        if(isset($response) && !$response->response->status) {
            throw new Exception('We couldn\'t parse your response of your API');
        }
        $this->fill($response->response->data);
        return $this;
    }


    /**
     * @param $model
     * @param $data
     * @return mixed
     * @throws BindingResolutionException
     */
    private function hydrate($model, $data)
    {
        $model = app()->make($model);
        return $model->fill($data);
    }


    /**
     * @param $model
     * @param $key
     * @param $foreign
     * @param $method
     * @return mixed
     * @throws BindingResolutionException
     */
    public function relationship($model, $key, $foreign, $method)
    {
        if (is_object($this->relations[$key]) && is_null($this->relations[$key])) {
            $data = (app()->make($model))->{$method}($foreign);
        } else {
            $data = $this->relations[$key];
        }
        return !is_array($data)?$this->hydrate($model, $data):collect($data);
    }

    /**
     * @param $data
     * @return $this
     */
    public function fill($data)
    {
        $this->setAttributes($data);
        $this->setRelations($data);
        return $this;
    }
}
