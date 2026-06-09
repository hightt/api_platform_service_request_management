<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REF = 'user-admin';
    public const TECH_ACTIVE_PREFIX = 'user-tech-active-';
    public const TECH_INACTIVE_PREFIX = 'user-tech-inactive-';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setUserName('admin');
        $adminUser->setRoles([UserRole::ADMIN]);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'admin123'));
        $manager->persist($adminUser);
        $this->addReference(self::ADMIN_USER_REF, $adminUser);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUserName(sprintf('tech_active_%d', $i));
            $user->setRoles([UserRole::TECHNICIAN]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'tech123'));
            $manager->persist($user);
            $this->addReference(self::TECH_ACTIVE_PREFIX . $i, $user);
        }

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUserName(sprintf('tech_inactive_%d', $i));
            $user->setRoles([UserRole::TECHNICIAN]);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'tech123'));
            $manager->persist($user);
            
            $this->addReference(self::TECH_INACTIVE_PREFIX . $i, $user);
        }

        $manager->flush();
    }
}