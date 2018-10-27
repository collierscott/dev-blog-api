<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);

        $manager->flush();
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        /** @var User $user */
        $user = $this->getReference('admin');

        $post = new BlogPost();
        $post->setTitle("This is a post!");
        $post->setSlug("this-is-a-post");
        $post->setAuthor($user);
        $post->setPublishedAt(new \DateTime("now"));
        $post->setContent("This is a post for you.");
        $manager->persist($post);

        $post = new BlogPost();
        $post->setTitle("This is another post!");
        $post->setSlug("this-is-another-post");
        $post->setAuthor($user);
        $post->setPublishedAt(new \DateTime("now"));
        $post->setContent("This is another post for you.");
        $manager->persist($post);
    }

    public function loadComments(ObjectManager $manager)
    {

    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("scollier67@hotmail.com")
            ->setName("Scott Collier")
            ->setUsername("scollier")
            ->setPassword($this->encoder->encodePassword($user, "password"));

        $this->addReference('admin', $user);

        $manager->persist($user);
        $manager->flush();
    }
}