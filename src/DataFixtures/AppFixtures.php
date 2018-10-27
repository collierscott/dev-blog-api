<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $post = new BlogPost();
        $post->setTitle("This is a post!");
        $post->setSlug("this-is-a-post");
        $post->setAuthor("Scott A Collier");
        $post->setPublishedAt(new \DateTime("now"));
        $post->setContent("This is a post for you.");
        $manager->persist($post);

        $post = new BlogPost();
        $post->setTitle("This is another post!");
        $post->setSlug("this-is-another-post");
        $post->setAuthor("Scott A Collier");
        $post->setPublishedAt(new \DateTime("now"));
        $post->setContent("This is another post for you.");
        $manager->persist($post);

        $manager->flush();
    }
}