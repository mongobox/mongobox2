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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $duration;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
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

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }
}
