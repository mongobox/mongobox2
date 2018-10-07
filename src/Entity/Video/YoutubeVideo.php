<?php

namespace App\Entity\Video;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\AbstractVideo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\YoutubeVideoRepository")
 */
class YoutubeVideo extends AbstractVideo
{
    const PATTERN_URL = "https://www.youtube.com/watch?v=%s";

    const PATTERN_URL_THUMBNAIL_HQ = "https://i.ytimg.com/vi/%s/hqdefault.jpg";

    const PATTERN_URL_THUMBNAIL = "https://i.ytimg.com/vi/%s/default.jpg";

    public function getUrl(): string
    {
        return sprintf(self::PATTERN_URL, $this->getProviderId());
    }

    public function getUrlThumbnail(): string
    {
        return sprintf(self::PATTERN_URL_THUMBNAIL, $this->getProviderId());
    }

    public function getUrlThumbnailHq(): string
    {
        return sprintf(self::PATTERN_URL_THUMBNAIL_HQ, $this->getProviderId());
    }
}
