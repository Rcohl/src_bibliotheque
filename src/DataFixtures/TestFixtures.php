<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadAuteurs();
        $this->loadEmprunts();
        $this->loadEmprunteurs();
        $this->loadGenres();
        $this->loadLivres();
    }

    public function loadAuteurs(): void
    {
        //données statiques
        $datas = [
            [
                'nom' => 'auteur',
                'prenom' => 'inconnu',
            ],
            [
                'nom' => 'Cartier',
                'prenom' => 'Hugues',
            ],
            [
                'nom' => 'Lambert',
                'prenom' => 'Armand',
            ],
            [
                'nom' => 'Moitessier',
                'prenom' => 'Thomas',
            ],
        ];

        foreach ($datas as $data) {
            $auteur = new Auteur();
            $auteur->setNom($data['nom']);
            $auteur->setPrenom($data['prenom']);

            $this->manager->persist($auteur);
        }

        //données dynamiques
        for ($i = 0; $i < 500; $i++) {
            $auteur = new Auteur();
            $words = random_int(1, 3);
            $auteur->setNom($this->faker->unique()->sentence($words));
            $words = random_int(1, 3);
            $auteur->setPrenom($this->faker->sentence($words));

            $this->manager->persist($auteur);
        }


        $this->manager->flush();
    }

    public function loadEmprunts(): void
    {
        $repository = $this->manager->getRepository(Emprunteur::class);
        $emprunteurs = $repository->findAll();

        $firstEmprunteur = $repository->find(1);
        $secondEmprunteur = $repository->find(2);
        $thirdEmprunteur = $repository->find(3);

        $repository = $this->manager->getRepository(Livre::class);
        $livres = $repository->findAll();

        $firstLivre = $repository->find(1);
        $secondLivre = $repository->find(2);
        $thirdLivre = $repository->find(3);

        //données statiques
        $datas = [
            [
                'dateEmprunt' => new DateTime('2020-02-01 10:00:00'),
                'dateRetour' => new DateTime('2020-03-01 10:00:00'),
                'emprunteur' => $firstEmprunteur,
                'livre' => $firstLivre,
            ],
            [
                'dateEmprunt' => new DateTime('2020-03-01 10:00:00'),
                'dateRetour' => new DateTime('2020-04-01 10:00:00'),
                'emprunteur' => $secondEmprunteur,
                'livre' => $secondLivre,
            ],
            [
                'dateEmprunt' => new DateTime('2020-04-01 10:00:00'),
                'dateRetour' => null,
                'emprunteur' => $thirdEmprunteur,
                'livre' => $thirdLivre,
            ],
        ];

        foreach ($datas as $data) {
            $emprunt = new Emprunt();
            $emprunt->setDateEmprunt($data['dateEmprunt']);
            $emprunt->setDateRetour($data['dateRetour']);

            $emprunt->setEmprunteur($data['emprunteur']);
            $emprunt->setLivre($data['livre']);

            $this->manager->persist($emprunt);
        }

        //données dynamiques
        for ($i = 0; $i < 200; $i++) {
            $emprunt = new Emprunt();
            $emprunt->setDateEmprunt($this->faker->dateTimeBetween('-3 months', '-2 months'));
            $emprunt->setDateRetour($this->faker->dateTimeBetween('-2 months', '-1 months'));

            $emprunteur = $this->faker->randomElement($emprunteurs);
            $emprunt->setEmprunteur($emprunteur);

            $livre = $this->faker->randomElement($livres);
            $emprunt->setLivre($livre);

            $this->manager->persist($emprunt);
        }


        $this->manager->flush();
    }


    // ! N'INJECTE PAS L'ADMIN DANS LA BASE DE DONNÉE !
    // public function loadUsers(): void
    // {
    //     $datas = [
    //         [
    //             'email' => 'admin@example.com',
    //             'roles' => ['ROLE_ADMIN'],
    //             'password' => '123',
    //             'enabled' => true,
    //         ],
    //     ];

    //     foreach ($datas as $data) {
    //         $user = new User();
    //         $user->setEmail($data['email']);
    //         $password = $this->hasher->hashPassword($user, $data['password']);
    //         $user->setPassword($password);
    //         $user->setRoles($data['roles']);
    //         $user->setEnabled($data['enabled']);

    //         $this->manager->persist($user);
    //     };
    // }

    public function loadEmprunteurs(): void
    {
        //données statiques
        $datas = [
            [
                'nom' => 'foo',
                'prenom' => 'foo',
                'tel' => 123456789,
                'email' => 'foo.foo@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'enabled' => true,
            ],
            [
                'nom' => 'bar',
                'prenom' => 'bar',
                'tel' => 123456789,
                'email' => 'bar.bar@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'enabled' => false,
            ],
            [
                'nom' => 'baz',
                'prenom' => 'baz',
                'tel' => 123456789,
                'email' => 'baz.baz@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'enabled' => true,
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);
            $user->setEnabled($data['enabled']);

            $this->manager->persist($user);

            $emprunteur = new Emprunteur();
            $emprunteur->setNom($data['nom']);
            $emprunteur->setPrenom($data['prenom']);
            $emprunteur->setTel($data['tel']);
            $emprunteur->setUser($user);

            $this->manager->persist($emprunteur);
        }

        $this->manager->flush();

        //données dynamiques
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            $user->setEnabled('enabled');

            $this->manager->persist($user);

            $emprunteur = new Emprunteur();
            $emprunteur->setNom($this->faker->lastName());
            $emprunteur->setPrenom($this->faker->firstName());
            $emprunteur->setTel(123456789);

            $emprunteur->setUser($user);

            $this->manager->persist($emprunteur);
        }

        $this->manager->flush();
    }

    public function loadGenres(): void
    {
        //données statiques
        $datas = [
            [
                'nom' => 'poésie',
                'description' => null,
            ],
            [
                'nom' => 'nouvelle',
                'description' => null,
            ],
            [
                'nom' => 'roman historique',
                'description' => null,
            ],
            [
                'nom' => 'roman d\'amour',
                'description' => null,
            ],
            [
                'nom' => 'roman d\' aventure',
                'description' => null,
            ],
            [
                'nom' => 'science-fiction',
                'description' => null,
            ],
            [
                'nom' => 'fantasy',
                'description' => null,
            ],
            [
                'nom' => 'biographie',
                'description' => null,
            ],
            [
                'nom' => 'conte',
                'description' => null,
            ],
            [
                'nom' => 'témoignage',
                'description' => null,
            ],
            [
                'nom' => 'théâtre',
                'description' => null,
            ],
            [
                'nom' => 'essai',
                'description' => null,
            ],
            [
                'nom' => 'journal intime',
                'description' => null,
            ],
        ];

        foreach ($datas as $data) {
            $genre = new Genre();
            $genre->setNom($data['nom']);
            $genre->setDescription($data['description']);

            $this->manager->persist($genre);
        }
        $this->manager->flush();
    }

    public function loadLivres(): void
    {
        $repository = $this->manager->getRepository(Auteur::class);
        $auteurs = $repository->findAll();

        $auteurInconnu = $repository->find(1);
        $cartierHugues = $repository->find(2);
        $lambertArmand = $repository->find(3);
        $moitessierThomas = $repository->find(4);

        $repository = $this->manager->getRepository(Genre::class);
        $genres = $repository->findAll();

        $poesie = $repository->find(1);
        $nouvelle = $repository->find(2);
        $romanHistorique = $repository->find(3);
        $romanAmour = $repository->find(4);

        //données statiques
        $datas = [
            [
                'titre' => 'Lorem ipsum dolor sit amet',
                'anneeEdition' => 2010,
                'nombrePages' => 100,
                'codeIsbn' => '9785786930024',
                'auteur' => $auteurInconnu,
                'genre' => $poesie,
            ],
            [
                'titre' => 'Consectetur adipiscing elit',
                'anneeEdition' => 2011,
                'nombrePages' => 150,
                'codeIsbn' => '9783817260935',
                'auteur' => $cartierHugues,
                'genre' => $nouvelle,
            ],
            [
                'titre' => 'Mihi quidem Antiochum',
                'anneeEdition' => 2012,
                'nombrePages' => 200,
                'codeIsbn' => '9782020493727',
                'auteur' => $lambertArmand,
                'genre' => $romanHistorique,
            ],
            [
                'titre' => 'Quem audis satis belle',
                'anneeEdition' => 2013,
                'nombrePages' => 250,
                'codeIsbn' => '9794059561353',
                'auteur' => $moitessierThomas,
                'genre' => $romanAmour,
            ],
        ];

        foreach ($datas as $data) {

            $livre = new Livre();
            $livre->setTitre($data['titre']);
            $livre->setAnneeEdition($data['anneeEdition']);
            $livre->setNombrePages($data['nombrePages']);
            $livre->setCodeIsbn($data['codeIsbn']);

            $livre->setAuteur($data['auteur']);
            $livre->addGenre($data['genre']);

            $this->manager->persist($livre);
        }

        $this->manager->flush();

        //données dynamiques
        for ($i = 0; $i < 1000; $i++) {

            $livre = new Livre();
            $words = random_int(1, 3);
            $livre->setTitre($this->faker->sentence($words));
            $livre->setAnneeEdition($this->faker->year());
            $livre->setNombrePages($this->faker->numberBetween(100, 300));
            $livre->setCodeIsbn($this->faker->isbn13());

            $auteur = $this->faker->randomElement($auteurs);
            $livre->setAuteur($auteur);

            $genre = $this->faker->randomElement($genres);
            $livre->addGenre($genre);

            $this->manager->persist($livre);
        }

        $this->manager->flush();
    }
}
