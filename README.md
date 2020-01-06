# Eloquent-for-API

![GitHub stars](https://img.shields.io/github/stars/luis199230/eloquent-for-api?style=plastic)

This package allow use models and relationships using resources of API how to entities.


## Installation 

```sh
composer require madeweb/eloquent-api
```

Generate model wrapper for call to api

```sh
php artisan make:model-api Model --api=API\\NameOfAPI 
```

Generate model wrapper for authentication with passport

```sh
php artisan make:model-api Model --api=API\\NameOfAPI --auth
```

## Configure

#### Use fillable in models
Is very important setup the fillable because this variable allow recognize the fields will have your model.

```sh
    protected $fillable = ['uuid', 'name', 'last_name', 'phone', 'document_type', 'document_number',
        'birthdate', 'civil_status', 'gender', 'address_uuid', 'user_uuid'];
```

#### Relationships
For prepare relations between model is required use this syntax. For example for the person model the order of parameters is class belongs, key for identify relationship, common key or field in both models, and method of API for connecting.

```sh
    public function user()
    {
        return $this->relationship(User::class, 'user', $this->user_uuid, 'find');
    }
```


#### Methods Basics
How it is an api, it is necessary to define own functions to call basic methods that exist in eloquent. For example, in this function, the showPerson method must exist in the API.

```sh
    public function find($uuid)
    {
        $response = $this->connection->showPerson($uuid);
        return $this->prepareResponse($response);
    }
 ```

