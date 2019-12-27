<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * User
 *
 * @ORM\Table(name="`user`")
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="uuid", type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $passwordHash;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $tokenResetPassword;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $authenticationKey;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusId;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;


    public function getUuid()
    {
        return $this->uuid;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getTokenResetPassword(): ?string
    {
        return $this->tokenResetPassword;
    }

    public function setTokenResetPassword(string $tokenResetPassword): self
    {
        $this->tokenResetPassword = $tokenResetPassword;
        return $this;
    }

    public function getAuthenticationKey(): ?string
    {
        return $this->authenticationKey;
    }

    public function setAuthenticationKey(string $authenticationKey): self
    {
        $this->authenticationKey = $authenticationKey;
        return $this;
    }

    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    public function setStatusId(int $statusId): self
    {
        $this->statusId = $statusId;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
