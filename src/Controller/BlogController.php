<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use  Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route(path="/blog")
 */
class BlogController extends AbstractController
{
    private $em;
    private $serializer;
    private $postRepository;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, BlogPostRepository $postRepository)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route(path="/{page}", name="blog_list", defaults={"page": 1}, requirements={"page"="\d+"})
     * @param $page
     * @param Request $request
     * @return JsonResponse
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
        $posts = $this->postRepository->findAll();

        return $this->json(
            [
                "page" => $page,
                'limit' => $limit,
                "data" => array_map(
                    function($item) {
                        /** @var BlogPost $item */
                        return $this->generateUrl('blog_post_by_slug', ['slug' => $item->getSlug()]);
                    }, $posts
                )
            ],
            200
        );
    }

    /**
     * @Route(path="/post/{id}", name="blog_post_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @param BlogPost $post
     * @return JsonResponse
     *
     * Can also use ParamConverter("post", class="App:BlogPost") with '@' symbol if don't type specify parameter.
     */
    public function post(BlogPost $post)
    {
        return $this->json(
            $post,
            Response::HTTP_OK
        );
    }

    /**
     * @Route(path="/post/{slug}", name="blog_post_by_slug", methods={"GET"})
     * @param BlogPost $post
     * @return JsonResponse
     *
     * Can also use ParamConverter("post", class="App:BlogPost", options={"mapping": {"slug": "slug}})
     *     with '@' symbol if don't type specify parameter.
     */
    public function postBySlug(BlogPost $post)
    {
        return $this->json(
            $post,
            Response::HTTP_OK
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
          Response::HTTP_CREATED
        );
    }

    /**
     * @param BlogPost $post
     *
     * @Route(path="/post/{id}", name="blog_post_delete_by_id", requirements={"id"="\d+"}, methods={"DELETE"})
     * @return JsonResponse
     */
    public function delete(BlogPost $post)
    {
        $this->em->remove($post);
        $this->em->flush();
        return $this->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}