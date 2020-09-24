<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    private $credentials=
        [
            ["email"=>"exampleAdmin@example.com", "password"=>"admin", "roles"=>["ROLE_ADMIN", "ROLE_USER"]],
            ["email"=>"exampleUser1@example.com", "password"=>"user", "roles"=>[ "ROLE_USER"]],
            ["email"=>"exampleUser2@example.com", "password"=>"user", "roles"=>[ "ROLE_USER"]],
            ["email"=>"exampleUser3@example.com", "password"=>"user", "roles"=>[ "ROLE_USER"]],
        ];
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->createUser($manager);
        $manager->flush();
    }

    public function createUser(ObjectManager $manager){
        foreach ($this->credentials as $credential){
            $newUser= new User();
            $newUser
                ->setEmail($credential["email"])
                ->setPassword($this->encoder->encodePassword($newUser, $credential["password"]))
                ->setRoles($credential["roles"]);
            $manager->persist($newUser);
        }
    }
}
