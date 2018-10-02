<?php

namespace App\Entity\Video;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\AbstractVideo;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\YoutubeVideoRepository")
 *
 */
class YoutubeVideo extends AbstractVideo
{
    const URL_PATTERN = 'https://www.youtube.com/watch?v=';
}
