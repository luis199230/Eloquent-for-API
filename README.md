# Eloquent-for-API
This package allow use models and relationships using resources of API how to entities.

## Installation 

```sh
composer require madeweb/eloquent-api
```
Generate model wrapper for call to api

```sh
php artisan make:model-api Model --api=API\\Name 
```

Generate model wrapper for authentication with passport

```sh
php artisan make:model-api Model --api=API\\Name --auth
```

