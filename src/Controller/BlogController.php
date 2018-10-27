<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use  Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="/blog")
 */
class BlogController extends AbstractController
{
    private $em;
    private $serializer;

    private const POSTS = [
        [
            "id" => 1,
            "slug" => "hello-world",
            "title" => "Hello World"
        ],
        [
            "id" => 2,
            "slug" => "hello-world-two",
            "title" => "Hello World Two"
        ],
        [
            "id" => 3,
            "slug" => "hello-world-three",
            "title" => "Hello World Three"
        ]
    ];
    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/{page}", name="blog_list", defaults={"page": 1}, requirements={"page"="\d+"})
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);

        return $this->json(
            [
                "page" => $page,
                'limit' => $limit,
                "data" => array_map(
                    function($item) {
                        return $this->generateUrl('blog_post_by_id', ['id' => $item['id']]);
                    }, self::POSTS
                )
            ],
            200
        );
    }

    /**
     * @Route(path="/post/{id}", name="blog_post_by_id", requirements={"id"="\d+"})
     */
    public function post($id)
    {
        return $this->json(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route(path="/post/{slug}", name="blog_post_by_slug")
     */
    public function postBySlug($slug)
    {
        return $this->json(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }

    /**
     * @param Request $request
     *
     * @Route(path="/add", name="blog_post_add", methods={"POST"})
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $post = $this->serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $this->em->persist($post);
        $this->em->flush();
        return $this->json(
          $post,
          200
        );
    }
}