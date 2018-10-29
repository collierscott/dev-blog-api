<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /** @var Factory $faker */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
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
        $this->loadComments($manager);

        $manager->flush();
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for($i = 0; $i < 100; $i++) {
            $id = rand(0, 9);
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30));
            $post->setSlug($this->faker->slug);
            $post->setAuthor($this->getReference("user_$id"));
            $post->setPublishedAt($this->faker->dateTimeThisYear);
            $post->setContent($this->faker->realText());

            $this->setReference("blog_post_$i", $post);

            $manager->persist($post);
        }
    }

    public function loadComments(ObjectManager $manager)
    {
        for($i = 0; $i < 100; $i++) {
            for($j = 0; $j < rand(1, 10); $j++) {
                $id = rand(0, 9);
                $comment = new Comment();
                $comment->setContent($this->faker->realText())
                    ->setPublishedAt($this->faker->dateTimeThisYear)
                    ->setPost($this->getReference("blog_post_$i"))
                    ->setAuthor($this->getReference("user_$id"));

                $this->setReference("comment_$i", $comment);

                $manager->persist($comment);
            }
        }
    }

    public function loadUsers(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email)
                ->setName($this->faker->firstName . " " . $this->faker->lastName)
                ->setUsername($this->faker->userName)
                ->setPassword($this->encoder->encodePassword($user, "passWord1"));

            $this->addReference("user_$i", $user);

            $manager->persist($user);
        }
    }
}