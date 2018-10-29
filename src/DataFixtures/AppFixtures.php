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
            $post = new BlogPost();
            $post->setTitle($this->faker->realText(30));
            $post->setSlug($this->faker->slug);
            $post->setAuthor($this->getRandomUserReference($post));
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
                /** @var BlogPost $post */
                $post = $this->getReference("blog_post_$i");
                $comment = new Comment();
                $comment->setContent($this->faker->realText())
                    ->setPost($post);
                $comment->setAuthor($this->getRandomUserReference($comment));
                $comment->setPublishedAt($this->faker->dateTimeThisYear);

                $this->setReference("comment_$i", $comment);

                $manager->persist($comment);
            }
        }
    }

    public function loadUsers(ObjectManager $manager)
    {
        $roles = [
            User::ROLE_COMMENTATOR,
            User::ROLE_WRITER,
            User::ROLE_EDITOR,
            User::ROLE_ADMIN,
            User::ROLE_SUPER_ADMIN
        ];

        $roleidx = 0;

        for($i = 0; $i < 10; $i++) {
            $user = new User();

            $username = str_replace('.', '_', $this->faker->userName);
            $user->setEmail($this->faker->email)
                ->setName($this->faker->firstName . " " . $this->faker->lastName)
                ->setUsername($username)
                ->setPassword($this->encoder->encodePassword($user, "passWord1"));

            if($roleidx >= count($roles)) {
                $roleidx = 0;
            }

            $role = $roles[$roleidx];
            $user->setRoles([$role]);
            $roleidx++;

            $this->addReference("user_$i", $user);

            $manager->persist($user);
        }
    }

    private function getRandomUserReference($entity) : User
    {
        /** @var User $user */
        $user = $this->getReference('user_' . rand(0, 9));

        if($entity instanceof BlogPost
            && !count(array_intersect($user->getRoles(),
                [User::ROLE_WRITER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]
                ))
        ) {
            return $this->getRandomUserReference($entity);
        }

        if($entity instanceof Comment
            && !count(array_intersect($user->getRoles(),
                [User::ROLE_WRITER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, User::ROLE_COMMENTATOR]
            ))
        ) {
            return $this->getRandomUserReference($entity);
        }

        return $user;
    }
}