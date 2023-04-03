<?php

namespace App\Controller;
use Psr\Log\LoggerInterface;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route ("/blog")
 */
class BlogController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    const POSTS = [
      [
          'id' => 1,
          'name' => 'First Post',
          'slug' => 'first-post',

      ],
      [
          'id' => 2,
          'name' => 'Second Post',
          'slug' => 'second-post',

      ],
      [
          'id' => 3,
          'name' => 'Third Post',
          'slug' => 'third-post',

      ],

    ];
    /**
     * @Route ("/{page}", name="app_blog_list", requirements={"page"="\d+"}, defaults={"page": 1})
     */
    public function list($page, Request $request): JsonResponse {
        $limit = $request->get('limit',10);

        return $this->json([
            'limit' => $limit,
            'page'=>  (int)$page,
            'data' => array_map( function ($item){
                return $this->generateUrl('app_blog_post',['id' => $item['id']]);
            },self::POSTS)
        ],404);
    }
    /**
     * @Route ("/post/{id}", name="app_blog_post", requirements={"id"="\d+"})
     */
    public function post($id):JsonResponse {
        return $this->json([
            'data' => array_filter(self::POSTS, function ($blog) use ($id) {
                return ($blog['id'] == $id);
            })
        ]);
    }
    /**
     * @Route ("/post/{slug}", name="app_blog_postbyslug")
     */
    public function postBySlug($slug):JsonResponse {
        return new JsonResponse([
            'data' => self::POSTS[array_search($slug, array_column(self::POSTS,'slug'))]
        ]);
    }

    /**
     * @Route ("/add", name="app_blog_add", methods={"POST"})
     */
    public function add(Request $request,LoggerInterface $logger, EntityManagerInterface $em, SerializerInterface $serializer):JsonResponse{

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em->persist($blogPost);
        $em->flush();

        return $this->json([
            'data' => $blogPost
        ]);
    }

}