<?php

namespace App\Videos;


use App\Entity\Video\YoutubeVideo;

class VideoFactory
{
    public function createVideoFromYoutubeData(YoutubeVideo $video = null, array $data): YoutubeVideo
    {
        if(is_null($video)){
            $video = new YoutubeVideo();
        }

        $updateDate = new \DateTime();

        $video
            ->setSourceName($data['title'])
            ->setDuration($data['duration'])
            ->setLastUpdatedDate($updateDate)
            ->setFoundOnProvider(true)
        ;

        return $video;
    }
}
