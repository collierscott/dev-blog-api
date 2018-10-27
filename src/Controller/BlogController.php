<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
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
     * @Route(path="/post/{id}", name="blog_post_by_id", requirements={"id"="\d+"})
     */
    public function post($id)
    {
        $post = $this->postRepository->find($id);
        return $this->json(
            $post
        );
    }

    /**
     * @Route(path="/post/{slug}", name="blog_post_by_slug")
     */
    public function postBySlug($slug)
    {
        $post = $this->postRepository->findOneBy(['slug' => $slug]);
        return $this->json(
            $post
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