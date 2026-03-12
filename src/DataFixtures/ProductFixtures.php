<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['name' => 'Iphone', 'price' => 10000],
            ['name' => 'Headphones', 'price' => 2000],
            ['name' => 'Case', 'price' => 1000],
        ];

        $currency = 'EUR';

        foreach ($products as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setCurrency($currency);

            $manager->persist($product);
        }

        $manager->flush();
    }
}