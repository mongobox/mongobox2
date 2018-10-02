<?php

namespace App\Entity\Video;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\AbstractVideo;

/**
 * @@ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DailymotionVideoRepository")
 */
class DailymotionVideo extends AbstractVideo
{


}
