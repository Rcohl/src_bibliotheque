<?php

namespace App\Controller;

use DateTime;
use App\Entity\Auteur;
use App\Entity\Emprunt;
use App\Entity\Emprunteur;
use App\Entity\Genre;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/user', name: 'app_test_user')]
    public function user(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $userRepository = $em->getRepository(User::class);
        $users = $userRepository->findBy([], ['email' => 'ASC']);

        // récupération des données de l'user dont l'id est 1
        $firstUser = $userRepository->find(1);

        // récupération des données de l'user dont l'email est foo.foo@example.com
        $userEmail = $userRepository->findOneBy([

            'email' => 'foo.foo@example.com',
        ]);

        // récupération des données de l'user dont l'attribut roles contient le mot clé 'ROLE_USER', trié par ordre alphabétique d'email
        $userRole = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_USER"%')
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();

        // récupération des données de l'user dont l'attribut enabled est égal à false, trié par ordre alphabétique d'email
        $userEnabled = $userRepository->createQueryBuilder('u')
            ->where('u.enabled LIKE :enabled')
            ->setParameter('enabled', 0)
            ->orderBy('u.email', 'ASC')
            ->getQuery()
            ->getResult();


        return $this->render('test/user.html.twig', [
            'users' => $users,
            'firstUser' => $firstUser,
            'userEmail' => $userEmail,
            'userRole' => $userRole,
            'userEnabled' => $userEnabled,
        ]);
    }

    #[Route('/livre', name: 'app_test_livre')]
    public function livre(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $livreRepository = $em->getRepository(Livre::class);
        $livres = $livreRepository->findBy([], ['titre' => 'ASC']);

        // récupération des données du livre dont l'id est 1
        $firstLivre = $livreRepository->find(1);

        // récupération de la liste des livres dont le titre contient le mot clé 'lorem', trié par ordre alphabétique de titre
        $livreTitre = $livreRepository->createQueryBuilder('l')
            ->where('l.titre LIKE :keyword')
            ->setParameter('keyword', '%lorem%')
            ->orderBy('l.titre', 'ASC')
            ->getQuery()
            ->getResult();



        // récupération de la liste des livres dont l'auteur à l'id 2, trié par ordre alphabétiques de titre
        $auteurRepository = $em->getRepository(Auteur::class);
        $auteur2 = $auteurRepository->find(2);
        $auteurLivres = $livreRepository->findByAuteur(
            ['auteur' => $auteur2],
            ['titre' => 'ASC']
        );


        // récupération de la liste des livres dont le genre contient le mot clé 'roman', trié par ordre alphabétiques de titre
        $genreRepository = $em->getRepository(Genre::class);
        $genreRoman = $genreRepository->createQueryBuilder('g')
            ->where('g.nom LIKE :keyword')
            ->setParameter('keyword', '%roman%')
            ->getQuery()
            ->getResult();

        if (!empty($genreRoman)) {
            $livresRepository = $em->getRepository(Livre::class);

            $livreRoman = $livresRepository->createQueryBuilder('l')
                ->leftJoin('l.genres', 'g')
                ->where('g IN (:genreRoman)')
                ->setParameter('genreRoman', $genreRoman)
                ->orderBy('l.titre', 'ASC')
                ->getQuery()
                ->getResult();
        }

        // Création d'un nouveau livre

        $auteur3 = $auteurRepository->find(2);
        $genre6 = $genreRepository->find(6);

        $newLivre = new Livre();
        $newLivre->setTitre('Totum autem id externum');
        $newLivre->setAnneeEdition('2020');
        $newLivre->setNombrePages('300');
        $newLivre->setCodeIsbn('9790412882714');
        $newLivre->setAuteur($auteur3);
        $newLivre->addGenre($genre6);
        $em->persist($newLivre);
        $em->flush();

        // récupération du livre dont l'id est 2 pour le modifié
        $livre2 = $livreRepository->find(2);
        $genre5 = $genreRepository->find(5);

        // modification du livre dont l'id est 2
        $livre2->setTitre('Aperiendum est igitur');
        $livre2->addGenre($genre5);
        $em->flush();

        // récupération du livre dont l'id est 123 pour le supprimé
        $livre123 = $livreRepository->find(123);

        // suppression le livre dont l'id est 123
        if ($livre123) {
            // suppression de l'objet
            $em->remove($livre123);
            $em->flush();
        }


        return $this->render('test/livre.html.twig', [
            'livres' => $livres,
            'firstLivre' => $firstLivre,
            'livreTitre' => $livreTitre,
            'auteurLivres' => $auteurLivres,
            'livreRoman' => $livreRoman,
            'newLivre' => $newLivre,
            'auteur3' => $auteur3,
            'genre6' => $genre6,
            'livre2' => $livre2,
            'genre5' => $genre5,
        ]);
    }

    #[Route('/emprunteur', name: 'app_test_emprunteur')]
    public function emprunteur(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $emprunteurRepository = $em->getRepository(Emprunteur::class);
        $emprunteurs = $emprunteurRepository->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        // récupération des données de l'emprunteur dont l'id est 3
        $emprunteur3 = $emprunteurRepository->find(3);

        // récupération des données de l'emprunteur qui est relié au user dont l'id est `3`
        // récupération de l'user dont l'id est 3

        // Récupérez l'utilisateur (User) par son ID
        $user3 = $em->getRepository(User::class)->find(3);

        // Récupérez l'emprunteur lié à cet utilisateur
        $userEmprunteur = $emprunteurRepository->findOneBy(['user' => $user3]);

        // récupération des données de l'emprunteur dont le nom ou le prénom contient le mot clé 'foo'
        $emprunteurFoo = $emprunteurRepository->createQueryBuilder('e')
            ->where('e.nom LIKE :keyword OR e.prenom LIKE :keyword')
            ->setParameter('keyword', '%foo%')
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        // récupération des données de l'emprunteur dont le téléphone contient le mot clé '1234'
        $telEmprunteur = $emprunteurRepository->createQueryBuilder('e')
            ->where('e.tel LIKE :keyword')
            ->setParameter('keyword', '%1234%')
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        // récupération de la liste des emprunteurs dont la date de création est antérieur au 01/03/2021

        $dateLimite = new \DateTime('2021-03-01');
        $createdAt = $emprunteurRepository->createQueryBuilder('e')
            ->where('e.createdAt < :dateLimite')
            ->setParameter('dateLimite', $dateLimite)
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('test/emprunteur.html.twig', [
            'emprunteurs' => $emprunteurs,
            'emprunteur3' => $emprunteur3,
            'userEmprunteur' => $userEmprunteur,
            'emprunteurFoo' => $emprunteurFoo,
            'telEmprunteur' => $telEmprunteur,
            'createdAt' => $createdAt,
        ]);
    }

    #[Route('/emprunt', name: 'app_test_emprunt')]
    public function emprunt(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $empruntRepository = $em->getRepository(Emprunt::class);

        // la liste des 10 derniers emprunts par ordre chronologique décroissant
        $derniersEmprunts = $empruntRepository->findBy([], ['dateEmprunt' => 'DESC'], 10);

        // la liste des emprunts dont l'emprunteur est l'emprunteur dont l'id est 2

        $emprunteur2 = $em->getRepository(Emprunteur::class)->find(2);
        $emprunts2 = $empruntRepository->findBy(['emprunteur' => $emprunteur2], ['dateEmprunt' => 'ASC']);

        // la liste des emprunts du livre dont l'id est 3
        $livre3 = $em->getRepository(Livre::class)->find(3);
        $emprunts3 = $empruntRepository->findBy(['livre' => $livre3], ['dateEmprunt' => 'DESC']);

        // la liste des 10 derniers emprunts qui ont été retournés
        $empruntsReturned = $empruntRepository->findBy([], ['dateRetour' => 'DESC'], 10);

        // la liste des emprunts qui non pas encore été retournés
        $empruntsNotReturned = $empruntRepository->findBy(['dateRetour' => null], ['dateEmprunt' => 'ASC']);

        // ajouter un nouvel emprunt
        // Récupérez l'emprunteur avec l'id 1 (foo foo)
        $emprunteurFoo = $em->getRepository(Emprunteur::class)->find(1);

        // Récupérez le livre avec l'id 1 (Lorem ipsum dolor sit amet)
        $livre1 = $em->getRepository(Livre::class)->find(1);

        // Créez un nouvel emprunt
        $newEmprunt = new Emprunt();
        $newEmprunt->setDateEmprunt(new \DateTime('2020-12-01 16:00:00'));
        $newEmprunt->setEmprunteur($emprunteurFoo);
        $newEmprunt->setLivre($livre1);

        $em->persist($newEmprunt);
        $em->flush();

        // modifier la date de retour de l'emprunt dont l'id est 3
        // Récupérez l'emprunt avec l'ID 3
        $thirdEmprunt = $em->getRepository(Emprunt::class)->find(3);

        // Mettez à jour la date de retour
        $newReturnedDate = new \DateTime('2020-05-01 10:00:00');
        $thirdEmprunt->setDateRetour($newReturnedDate);

        // Persistez les modifications
        $em->flush();

        // supprimer l'emprunt dont l'id est 42
        // Récupérez l'emprunt avec l'ID 42
        $emprunt42 = $em->getRepository(Emprunt::class)->find(42);

        // suppression de l'emprunt
        if ($emprunt42) {
            $em->remove($emprunt42);
            $em->flush();
        }


        return $this->render('test/emprunt.html.twig', [
            'derniersEmprunts' => $derniersEmprunts,
            'emprunts2' => $emprunts2,
            'emprunteur2' => $emprunteur2,
            'emprunts3' => $emprunts3,
            'livre3' => $livre3,
            'empruntsReturned' => $empruntsReturned,
            'empruntsNotReturned' => $empruntsNotReturned,
            'newEmprunt' => $newEmprunt,
            'livre1' => $livre1,
            'emprunteurFoo' => $emprunteurFoo,
            'thirdEmprunt' => $thirdEmprunt,
            'newReturnedDate' => $newReturnedDate,
        ]);
    }
}
