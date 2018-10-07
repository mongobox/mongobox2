<?php

namespace App\Videos;

use App\Repository\VideoRepository;
use Doctrine\Common\Persistence\ObjectManager;

class VideoManager
{
    private $em;

    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    private function getVideoRepository()
    {
        return $this->em->getRepository(VideoRepository::class);
    }

    public function getVideosNeedUpdate()
    {
        $this->videos = $this->getVideoRepository()->findAll();
        $this->nbVideos = count($this->videos);

//        $this->tagReplace = $this->em->getRepository('MongoboxJukeboxBundle:VideoTag')
//            ->findOneBy(array('system_name' => VideoTag::VIDEO_TAG_REPLACE));
    }
}
