<?php

namespace App\DataFixtures;

use App\Entity\Video\YoutubeAbstractVideo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class VideosFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $youtubeVideo = new YoutubeAbstractVideo();
         $youtubeVideo
             ->setName('test-video-1')
             ->setProviderId('ukAsYp-pW5Q')
         ;

         $manager->persist($youtubeVideo);

        $youtubeVideo = new YoutubeAbstractVideo();
        $youtubeVideo
            ->setName('test-video-2')
            ->setProviderId('b4ozdiGys5g')
        ;

        $manager->persist($youtubeVideo);

        $youtubeVideo = new YoutubeAbstractVideo();
        $youtubeVideo
            ->setName('test-video-3')
            ->setProviderId('oIdPPVkkHYs')
        ;

        $manager->persist($youtubeVideo);

        $youtubeVideo = new YoutubeAbstractVideo();
        $youtubeVideo
            ->setName('test-video-4')
            ->setProviderId('oysMt8iL9UE')
        ;

        $manager->persist($youtubeVideo);

        $youtubeVideo = new YoutubeAbstractVideo();
        $youtubeVideo
            ->setName('test-video-5')
            ->setProviderId('-MSFX7uZiAk')
        ;

        $manager->persist($youtubeVideo);

        $manager->flush();
    }
}
