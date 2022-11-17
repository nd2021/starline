<?php

namespace StarLine;

class Config
{
    private string $app_id = '';
    private string $app_secret = '';

    private string $login = '';
    private string $password = '';

    private string $app_code = '';
    private string $app_token = '';

    private string $slid_token = '';

    private string $slnet_token = '';
    private string $user_id = '';

    /**
     * Проверить наличие основных параметров
     *
     * @return bool
     */
    public function checkMain(): bool
    {
        return !(
            empty($this->getAppId()) ||
            empty($this->getAppSecret()) ||
            empty($this->getLogin()) ||
            empty($this->getPassword())
        );
    }
    /**
     * Проверить наличие API токена и id пользователя
     *
     * @return bool
     */
    public function checkAPIToken(): bool
    {
        return !(
            empty($this->getUserId()) ||
            empty($this->getSlnetToken())
        );
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getAppId(): string
    {
        return $this->app_id;
    }

    public function setAppId(string $app_id): self
    {
        $this->app_id = $app_id;
        return $this;
    }

    public function getAppSecret(): string
    {
        return $this->app_secret;
    }

    public function setAppSecret(string $app_secret): self
    {
        $this->app_secret = $app_secret;
        return $this;
    }

    public function getAppCode(): string
    {
        return $this->app_code;
    }

    public function setAppCode(string $app_code): self
    {
        $this->app_code = $app_code;
        return $this;
    }

    public function getAppToken(): string
    {
        return $this->app_token;
    }

    public function setAppToken(string $app_token): self
    {
        $this->app_token = $app_token;
        return $this;
    }

    public function getSlidToken(): string
    {
        return $this->slid_token;
    }

    public function setSlidToken(string $slid_token): self
    {
        $this->slid_token = $slid_token;
        return $this;
    }

    public function getSlnetToken(): string
    {
        return $this->slnet_token;
    }

    public function setSlnetToken(string $slnet_token): self
    {
        $this->slnet_token = $slnet_token;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): Config
    {
        $this->user_id = $user_id;
        return $this;
    }
}