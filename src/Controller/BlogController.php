<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
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

    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 1}, requirements={"page"="\d+"})
     */
    public function list($page)
    {
        return new JsonResponse(
            [
                "page" => $page,
                "data" => array_map(
                    function($item) {
                        return $this->generateUrl('blog_by_id', ['id' => $item['id']]);
                    }, self::POSTS
                )
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     */
    public function post($id)
    {
        return new JsonResponse(
            self::POSTS[array_search($id, array_column(self::POSTS, 'id'))]
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug")
     */
    public function postBySlug($slug)
    {
        return new JsonResponse(
            self::POSTS[array_search($slug, array_column(self::POSTS, 'slug'))]
        );
    }
}