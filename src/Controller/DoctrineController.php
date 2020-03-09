<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/doctrine")
 */
class DoctrineController extends AbstractController
{

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

    /**
     * @Route("/user/{id}")
     * Retourne un objet Member dont les attributs sont settés à partir de la table member avec une clause WHERE
     * sur l'id ou null si l'id n'existe pas dans la table
     *
     */
    public function getOneUser(MemberRepository $repository, $id)
    {
        $member = $repository->find($id);

        dump($member);
        return $this->render('doctrine/get_one_user.html.twig',
            [
                'member' => $member
            ]);
    }

    /**
     * @Route("/member/list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listMembers(MemberRepository $repository)
    {
        /*
         * Retourne tous les membres de la table member sous la form d'un tableau d'objets Member
         *
         */
        // $members = $repository->findAll();
        
        // Pour ajouter un tri, findBy sans filtre ce qui revien au findAll :
        $members = $repository->findBy([], ['id' => 'ASC']);
        return $this->render('doctrine/list_members.html.twig',
            [
                'members' => $members
            ]);

    }

    /**
     * @Route("/search-email")
     */

    public function searchEmail(Request $request, MemberRepository $repository)
    {
        // if(isset($_GET['email']
        if ($request->query->has('email')){

            $twigVariables = [];
            // findOneBy quand on est sûr qu'il n'y aura pas plus d'un resultat
            // retourne un objet Member ou null
           $member = $repository->findOneBy(
               [
                   'email' => $request->query->get('email')
               ]);
           $twigVariables['member'] = $member;
        }
        return $this->render("doctrine/search_emmail.html.twig",
            $twigVariables
        );
    }

    /**
     * @Route("/member/pseudo/{pseudo}")
     * 
     */
    public function getByPseudo(MemberRepository $repository, $pseudo)
    {
        // retourne un tableaux d'objets Member filtrés par le pseudo
        $members = $repository->findBy(
            ['pseudo'=> $pseudo], // filtre (clause WHERE)
            ['birthdate' => 'DESC'] // optionnel, pour ajouter un ORDER BY
            //5, LIMIT
            //10 // LIMIT
            
        );
        
        return $this->render('doctrine/list_members.html.twig',
            [
                'members' => $members
            ]);
        
    }

    /**
     * @Route("/create")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMember(Request $request, EntityManagerInterface $manager)
    {
        // Si le formulaire a été envoyé
        if ($request->isMethod('POST')){
            // $data contient Tout $_POST
            $data = $request->request->all();

            $member = new Member();
            $member
                ->setPseudo($data['pseudo'])
                ->setEmail($data['email'])

                // Le setter de birthdate attend un objet DateTime
                ->setBirthdate(new \DateTime($data['birthdate']))
            ;

            // Indique au gestionnaire d'entités qu'il faudra enregistrer membre au prochain flush
            // ne fait pas l'enregistrement en base mais prépare cette enregistrement (on peut en mettre plusieurs)
            // persist() fait un insert ou un update en fonction de la valeur de l'attribut id(null = insert)
            $manager->persist($member);
            // enregistrement en bdd de tous les persist
            $manager->flush();
        }
        return $this->render('doctrine/create_member.html.twig');
    }

    /**
     * @Route ("/member/search")
     */
    public function search()
    {

    }
}
