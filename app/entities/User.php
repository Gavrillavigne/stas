<?php

namespace app\entities;

class User
{
    /**
     * @param $id
     * @param $name
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $password
     * @param $status
     * @param $oauthClient
     * @param $oauthClientUserId
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($id = null, $name = null, $firstName = null, $lastName = null, $email = null, $password = null, $status = null, $oauthClient = null, $oauthClientUserId = null, $createdAt = null, $updatedAt = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->oauthClient = $oauthClient;
        $this->oauthClientUserId = $oauthClientUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /** @var int|null */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var int */
    private $status;

    /** @var string|null */
    private $oauthClient;

    /** @var int|null */
    private $oauthClientUserId;

    /** @var int */
    private $createdAt;

    /** @var int */
    private $updatedAt;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function getOauthClient(): ?string
    {
        return $this->oauthClient;
    }

    /**
     * @return int|null
     */
    public function getOauthClientUserId(): ?int
    {
        return $this->oauthClientUserId;
    }
}