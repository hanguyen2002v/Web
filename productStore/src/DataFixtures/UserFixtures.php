<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    //Khai thư viện mã hóa mật khẩu
    private $hasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->hasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        //Tài khoản của admin
        $user = new User;
        $user->setUserName("admin");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $manager->persist($user);

        //Tài Khoản của người dùng
        $user = new User;
        $user->setUserName("customer");
        $user->setRoles(['ROLE_CUSTOMER']);
        $user->setPassword($this->hasher->hashPassword($user, "123456"));
        $manager->persist($user);

        $manager->flush();
    }
}
