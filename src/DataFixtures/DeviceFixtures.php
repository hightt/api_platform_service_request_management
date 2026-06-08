<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Device;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeviceFixtures extends Fixture
{
    public const DEVICE_PREFIX = 'device-';

    private array $models = [
        'MacBook Pro M3', 'Dell XPS 15', 'ThinkPad X1 Carbon', 'HP EliteBook 840', 
        'Asus ROG Zephyrus', 'iPhone 15 Pro', 'iPad Pro 12.9', 'Samsung Galaxy S24', 
        'Sony Bravia 65', 'Cisco Router Catalyst'
    ];

    private array $customers = [
        'ACME Corp', 'Stark Industries', 'Wayne Enterprises', 'Cyberdyne Systems', 
        'Umbrella Corp', 'Tyrell Corp', 'Initech LLC', 'Hooli Inc', 'Globex Corporation', 'Oscorp'
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $device = new Device();
            $device->setSerialNumber(sprintf('SN-REV2026-%04d', $i * 123));
            $device->setModel($this->models[$i - 1]);
            $device->setCustomerName($this->customers[$i - 1]);
            
            $manager->persist($device);
            $this->addReference(self::DEVICE_PREFIX . $i, $device);
        }

        $manager->flush();
    }
}