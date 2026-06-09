<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Technician;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TechnicianFixtures extends Fixture implements DependentFixtureInterface
{
    public const TECH_ACTIVE_PREFIX = 'tech-active-';

    /**
    * @var array<int, string>
    */
    private array $firstNames = ['John', 'Adam', 'Robert', 'David', 'James', 'Michael', 'William', 'Richard', 'Thomas', 'Charles'];

    /**
    * @var array<int, string>
    */
    private array $lastNames = ['Doe', 'Smith', 'Tailor', 'Miller', 'Black', 'White', 'Walker', 'Green', 'Evans', 'Stone'];

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            /** @var User $user */
            $user = $this->getReference(UserFixtures::TECH_ACTIVE_PREFIX . $i, User::class);
            
            $tech = new Technician();
            $tech->setFirstName($this->firstNames[$i - 1]);
            $tech->setLastName($this->lastNames[$i - 1]);
            $tech->setEmail(sprintf('%s.%s@service.com', strtolower($tech->getFirstName()), strtolower($tech->getLastName())));
            $tech->setActive(true);
            $tech->setLogin($user);
            
            $manager->persist($tech);
            $this->addReference(self::TECH_ACTIVE_PREFIX . $i, $tech);
        }

        for ($i = 1; $i <= 10; $i++) {
            /** @var User $user */
            $user = $this->getReference(UserFixtures::TECH_INACTIVE_PREFIX . $i, User::class);
            
            $tech = new Technician();
            $tech->setFirstName($this->firstNames[($i - 1 + 3) % 10]);
            $tech->setLastName('Inactive');
            $tech->setEmail(sprintf('inactive.%s%d@service.com', strtolower($tech->getFirstName()), $i));
            $tech->setActive(false);
            $tech->setLogin($user);
            
            $manager->persist($tech);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}