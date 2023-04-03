<?php

namespace App\Controller;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route ("/blog")
 */
class BlogController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{


    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $em){}

    /**
     * @Route ("/{page}", name="app_blog_list", requirements={"page"="\d+"}, defaults={"page": 1})
     */
    public function list($page, Request $request): JsonResponse {
        $limit = $request->get('limit',10);
        $blogPosts = $this->em->getRepository(BlogPost::class)->findAll();

        return $this->json([
            'limit' => $limit,
            'page'=>  (int)$page,
            'data' => array_map( function (BlogPost $item){
                return $this->generateUrl('app_blog_postbyslug',['slug' => $item->getSlug()]);
            },$blogPosts)
        ],200);
    }
    /**
     * @Route ("/post/{id}", methods={"GET"}, name="app_blog_post", requirements={"id"="\d+"})
     */
    public function post(BlogPost $post):JsonResponse {

        return $this->json([
            'data' => $post
        ],200);
    }
    /**
     * @Route ("/post/{slug}", name="app_blog_postbyslug", methods={"GET"})
     */
    public function postBySlug(BlogPost $post):JsonResponse {

        // $this->em->getRepository(BlogPost::class)->findOneBy(['slug' => $slug]);

        return $this->json(['data' => $post],200);
    }

    /**
     * @Route ("/add", name="app_blog_add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $em):JsonResponse{

        $blogPost = $this->serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em->persist($blogPost);
        $em->flush();

        return $this->json([
            'data' => $blogPost
        ]);
    }

    /**
     * @Route ("/post/{id}", name="app_blog_delete", methods={"DELETE"})
     */

    public function delete(BlogPost $id): JsonResponse
    {
        $blogPost = $this->em->getRepository(BlogPost::class)->find($id);
        $this->em->remove($blogPost);
        $this->em->flush();

        return $this->json(null, 200);
    }

}