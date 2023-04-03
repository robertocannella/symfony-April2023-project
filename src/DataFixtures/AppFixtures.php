<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle("A first Post!");
        $blogPost->setPublished(new \DateTime('2023-04-03 12:00:00'));
        $blogPost->setContent('Post text!');
        $blogPost->setAuthor('Rob Can');
        $blogPost->setSlug('a-first-post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle("A Second Post!");
        $blogPost->setPublished(new \DateTime('2023-04-03 12:04:00'));
        $blogPost->setContent('Post text!');
        $blogPost->setAuthor('Rob Can');
        $blogPost->setSlug('a-second-post');

        $manager->persist($blogPost);

        $manager->flush();
    }
}
