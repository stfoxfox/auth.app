<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
{
    const TYPE_BROWSER = 0;
    const TYPE_MOBILE = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="uuid", type="guid")
     */
    private $uuid;

    /**
     * @ORM\Column(type="integer")
     */
    private $deviceType;

    /**
     * @ORM\Column(type="integer")
     */
    private $deviceModel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tokenPushMessages;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\App")
     * @ORM\JoinColumn(name="id_app", referencedColumnName="id", nullable=false)
     */
    private $application;

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getDeviceType(): ?int
    {
        return $this->deviceType;
    }

    public function setDeviceType(int $deviceType): self
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    public function getDeviceModel(): ?string
    {
        return $this->deviceModel;
    }

    public function setDeviceModel(string $deviceModel): self
    {
        $this->deviceModel = $deviceModel;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

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

    public function getTokenPushMessages(): ?string
    {
        return $this->tokenPushMessages;
    }

    public function setTokenPushMessages(string $tokenPushMessages): self
    {
        $this->tokenPushMessages = $tokenPushMessages;

        return $this;
    }

    public function getApplication(): App
    {
        return $this->application;
    }

    public function setApplication(App $application): self
    {
        $this->application = $application;

        return $this;
    }
}
