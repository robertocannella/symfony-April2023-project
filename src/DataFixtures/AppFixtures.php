<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    protected Generator $faker;
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);

    }
    public function loadBlogPosts(ObjectManager $manager){
        $user = $this->getReference('user_admin');


        for ($i=0;$i<100;++$i){
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTimeBetween('-100 days','-1 days'));
            $blogPost->setContent($this->faker->paragraph(1));
            $blogPost->setAuthor($user);
            $blogPost->setSlug($this->faker->slug());

            $this->setReference("blog_post_id_$i",$blogPost);
            $manager->persist($blogPost);
        }

        $manager->flush();
    }
    public function loadComments(ObjectManager $manager){
            for ($i=0; $i<100; ++$i){
                $numComments = rand(1,10);
                for ($j=0; $j<$numComments; ++$j) {
                    $randUserId = rand(0, 9);
                    $randPostId = rand(0, 99);

                    $comment = new Comment();
                    $comment->setAuthor($this->getReference("user_id_$randUserId"));
                    $comment->setContent($this->faker->realText());
                    $comment->setPublished($this->faker->dateTimeBetween('-100 days','-1 days'));
                    $comment->setBlogPost($this->getReference("blog_post_id_$randPostId"));


                    $manager->persist($comment);
                }
            }
            $manager->flush();
    }
    public function loadUsers(ObjectManager $manager){
        $user = new User();
        $user->setEmail('robertocannella@gmail.com');
        $user->setName('Roberto Cannella');
        $user->setUsername('admin');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'Secret123'
        );
        $user->setPassword($hashedPassword);

        $this->addReference('user_admin', $user);
        $manager->persist($user);

        for ($i=0;$i<10;++$i){
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setName($this->faker->name);
            $user->setUsername($this->faker->userName);
            $user->setFirstName($this->faker->firstName);
            $user->setPassword($this->faker->password);
            $this->setReference("user_id_$i", $user);
            $manager->persist($user);

        }

        $manager->flush();
    }
}
