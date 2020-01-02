<?php

namespace Madeweb\Eloquent\API\Models\Traits;

trait AuthenticatableTrait
{
    /**
     * @param $email
     * @param $role
     * @return $this
     */
    public function findForPassport($email, $role)
    {
        $response = $this->connection->showUserByEmail($email, $role);
        return $this->prepareResponse($response);
    }

    /**
     * @param array $credentials
     * @return $this
     */
    public function fetchUserByCredentials($credentials = [])
    {
        $response = $this->connection->login($credentials['email'], $credentials['password'], $credentials['role']);
        return $this->prepareResponse($response);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return "uuid";
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return isset($this->attributes[$this->getAuthIdentifierName()])?$this->attributes[$this->getAuthIdentifierName()]:null;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        if (!empty($this->getRememberTokenName())) {
            return $this->attributes[$this->getRememberTokenName()];
        }
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        if (!empty($this->getRememberTokenName())) {
            $this->attributes[$this->getRememberTokenName()] = $value;
        }
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
