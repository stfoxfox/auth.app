<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SocialAccountRepository")
 */
class SocialAccount
{

    const SOCIAL_FACEBOOK = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="uuid", type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $socialId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenSecret;

    /**
     * @ORM\Column(type="integer", nullable=true, nullable=true)
     */
    private $typeSocialNetwork;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="uuid_user", referencedColumnName="uuid", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getSocialId(): ?string
    {
        return $this->socialId;
    }

    public function setSocialId(string $socialId): self
    {
        $this->socialId = $socialId;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getTokenSecret(): ?string
    {
        return $this->tokenSecret;
    }

    public function setTokenSecret(string $tokenSecret): self
    {
        $this->tokenSecret = $tokenSecret;

        return $this;
    }

    public function getTypeSocialNetwork(): ?int
    {
        return $this->typeSocialNetwork;
    }

    public function setTypeSocialNetwork(int $typeSocialNetwork): self
    {
        $this->typeSocialNetwork = $typeSocialNetwork;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
