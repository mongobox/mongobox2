<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="videos")
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="provider", type="string", length=20)
 * @ORM\DiscriminatorMap({
 *     "youtube" = "\App\Entity\Video\YoutubeVideo",
 *     "dailymotion" = "\App\Entity\Video\DailymotionVideo"
 * })
 */
abstract class AbstractVideo
{
    const YOUTUBE_PROVIDER = 'youtube';
    const DAILYMOTION_PROVIDER = 'dailymotion';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_id", type="string", length=50, nullable=true)
     */
    protected $providerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $sourceName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastUpdatedDate;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    protected $foundOnProvider = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $providerId
     */
    public function setProviderId(?string $providerId): self
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderId(): ?string
    {
        return $this->providerId;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceName(): ?string
    {
        return $this->sourceName;
    }

    /**
     * @param mixed $sourceName
     * @return AbstractVideo
     */
    public function setSourceName(string $sourceName)
    {
        $this->sourceName = $sourceName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastUpdatedDate(): ?\DateTime
    {
        return $this->lastUpdatedDate;
    }

    /**
     * @param mixed $lastUpdatedDate
     * @return AbstractVideo
     */
    public function setLastUpdatedDate(\DateTime $lastUpdatedDate): self
    {
        $this->lastUpdatedDate = $lastUpdatedDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isFoundOnProvider(): bool
    {
        return $this->foundOnProvider;
    }

    /**
     * @param mixed $foundOnProvider
     * @return AbstractVideo
     */
    public function setFoundOnProvider(bool $foundOnProvider): self
    {
        $this->foundOnProvider = $foundOnProvider;

        return $this;
    }
}
