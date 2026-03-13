<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Audio', 'slug' => 'audio'],
            ['name' => 'Peripherals', 'slug' => 'peripherals'],
            ['name' => 'Laptops', 'slug' => 'laptops'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
        ];

        foreach ($categories as $i => $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setSlug($data['slug']);
            $manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }

        $products = [
            ['name' => 'Sony WH-1000XM5', 'description' => 'Industry leading noise cancelling headphones', 'price' => 29999, 'stock' => 50, 'slug' => 'sony-wh-1000xm5', 'category' => 0],
            ['name' => 'AirPods Pro 2', 'description' => 'Active noise cancellation earbuds by Apple', 'price' => 24999, 'stock' => 30, 'slug' => 'airpods-pro-2', 'category' => 0],
            ['name' => 'Logitech MX Master 3', 'description' => 'Advanced wireless mouse for professionals', 'price' => 9999, 'stock' => 75, 'slug' => 'logitech-mx-master-3', 'category' => 1],
            ['name' => 'Keychron K2 Keyboard', 'description' => 'Compact wireless mechanical keyboard', 'price' => 8999, 'stock' => 40, 'slug' => 'keychron-k2', 'category' => 1],
            ['name' => 'MacBook Pro 14"', 'description' => 'Apple M3 Pro chip, 18GB RAM, 512GB SSD', 'price' => 199900, 'stock' => 15, 'slug' => 'macbook-pro-14', 'category' => 2],
            ['name' => 'Dell XPS 15', 'description' => 'Intel Core i7, 16GB RAM, OLED display', 'price' => 159900, 'stock' => 20, 'slug' => 'dell-xps-15', 'category' => 2],
            ['name' => 'Anker 65W Charger', 'description' => 'Compact fast charger with USB-C', 'price' => 3999, 'stock' => 100, 'slug' => 'anker-65w-charger', 'category' => 3],
            ['name' => 'USB-C Hub 7-in-1', 'description' => 'HDMI, USB-A, SD card reader and more', 'price' => 4999, 'stock' => 60, 'slug' => 'usb-c-hub-7in1', 'category' => 3],
            ['name' => 'Samsung 27" Monitor', 'description' => '4K UHD IPS panel with 144Hz refresh rate', 'price' => 49999, 'stock' => 25, 'slug' => 'samsung-27-monitor', 'category' => 1],
            ['name' => 'Elgato Stream Deck', 'description' => 'Customizable LCD keys for streamers and creators', 'price' => 14999, 'stock' => 35, 'slug' => 'elgato-stream-deck', 'category' => 3],
        ];

        foreach ($products as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);
            $product->setStock($data['stock']);
            $product->setSlug($data['slug']);
            $product->setCategory($this->getReference('category_' . $data['category'], Category::class));
            $manager->persist($product);
        }

        $manager->flush();
    }
}