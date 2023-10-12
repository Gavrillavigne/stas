<?php

namespace app\entities;

class File
{
    /**
     * @param $id
     * @param $userId
     * @param $baseUrl
     * @param $path
     * @param $type
     * @param $name
     * @param $isSecure
     * @param $expirationTime
     * @param $createdAt
     * @param $updatedAt
     */
    public function __construct($id, $userId, $baseUrl, $path, $type, $name, $isSecure, $expirationTime, $createdAt, $updatedAt)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->baseUrl = $baseUrl;
        $this->path = $path;
        $this->type = $type;
        $this->name = $name;
        $this->isSecure = $isSecure;
        $this->expirationTime = $expirationTime;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /** @var int */
    private $id;

    /** @var int */
    private $userId;

    /** @var string */
    private $baseUrl;

    /** @var string */
    private $path;

    /** @var string */
    private $type;

    /** @var string */
    private $name;

    /** @var int */
    private $isSecure;

    /** @var int|null */
    private $expirationTime;

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
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function isSecure(): int
    {
        return $this->isSecure;
    }

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int
    {
        return $this->expirationTime;
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

}