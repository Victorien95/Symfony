<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DoctrineController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('doctrine/index.html.twig', [
            'controller_name' => 'DoctrineController',
        ]);
    }

    /**
     * @Route("/dbal")
     */
    public function dbal(Connection $dbal)
    {
        // utilisable comme PDO:
        $stmt = $dbal->query("SELECT * FROM abonne WHERE id=1");
        dump($stmt->fetch());

        // query et fetch dans la même méthode avec bindvalue:
        $abonne1 = $dbal->fetchAssoc("SELECT * FROM abonne WHERE id=1", [
            ':id'=> 1
        ]);
        dump($abonne1);
        $abonnes = $dbal->fetchAll('SELECT * FROM abonne');
        dump($abonnes);
        $nb = $dbal->fetchColumn('SELECT count(*) FROM abonne');
        dump($nb);

        // INSERT INTO abonne (prenom) VALUES ('Laurence')
        // $dbal->insert('abonne', ['prenom' => 'Laurence']);

        //UPDATE abonne SET prenom = 'Jules' WHERE id =1
        $dbal->update('abonne', ['prenom' => 'Jules'], ['id' => 1]);

        // DELETE FROM abonne WHERE id = 5
        $dbal->delete('abonne', ['id' => 5]);

        return $this->render('doctrine/dbal.html.twig');
    }
}
