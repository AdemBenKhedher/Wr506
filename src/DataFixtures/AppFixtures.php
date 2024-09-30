<?php

namespace App\DataFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use DateTimeImmutable;
use DateTime;
use App\Entity\Actor;
use App\Entity\Movie;
use App\Entity\Category;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    { 
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Person($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));
    
        $actors = $faker->actors($gender = null, $count = 190, $duplicates = false);
        $createdActors = [];
        foreach ($actors as $item) {
            $fullname = $item;
            $fullnameExploded = explode(' ', $fullname);

            $firstname = $fullnameExploded[0]; 
            $lastname = $fullnameExploded[1]; 

            $actor = new Actor();
            $actor->setLastname($lastname);
            $actor->setFirstname($firstname);
            $actor->setDob($faker->dateTimeThisCentury());
            $actor->setAwards($faker->numberBetween(0, 10));
            $actor->setBio($faker->text(200)); 
            $actor->setNationalty($faker->country());
            $dob = $actor->getDob();
            $now = new DateTime();
            $dod = null;
            $dod = $faker->optional(0.2)->dateTimeBetween($dob, $now);
            $actor->setDod($dod);
            $actor->setMedia($faker->imageUrl(640, 480, 'actors', true));
            $actor->setGender($faker->randomElement(['male','female','other']));
            $actor->setCreatedAt(new DateTimeImmutable());
            $createdActors[] = $actor;
            $manager->persist($actor);


        }

        $categories = [];
        $genres = $faker->movieGenres(21);
        foreach ($genres as $genreTitle) {
            $category = new Category();
            $category->setTitle($genreTitle);
            $category->setCreatedAt(new DateTimeImmutable());
            $manager->persist($category);
            $categories[] = $category;
        }

        $movies = $faker->movies(100);
        foreach ($movies as $item) {
            $movie = new Movie();
            $movie->setTitle($item);
            $movie->setDescription($faker->overview(200));
            $movie->setReleaseDate($faker->dateTimeThisCentury());
            $movie->setDuration($faker->numberBetween(1,480));
            $movie->setEntries($faker->numberBetween(0, 1000000));
            $movie->setDirector($faker->name());
            $movie->setRating($faker->randomFloat(1, 0, 10));
            $movie->setmedia($faker->imageUrl(640, 480, 'movies', true));
            $movie->setCreatedAt(new DateTimeImmutable());


            shuffle($createdActors);
            $createdActorsSliced = array_slice($createdActors, 0, 4);
            foreach ($createdActorsSliced as $actor) {
                $movie->addActor($actor);
            }

            shuffle($categories);
            $categoriesSliced = array_slice($categories, 0, mt_rand(1, 3));
            foreach ($categoriesSliced as $category) {
                $movie->addCategory($category);
            }
            $movies[]= $movie;
            $manager->persist($movie);
        }

        $manager->flush();
    }
}
