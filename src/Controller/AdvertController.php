<?php
// src/Controller/AdvertController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\Antispam;
use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\Application;
use App\Entity\Category;
use App\Entity\Skill;
use App\Entity\AdvertSkill;
use App\Form\AdvertType;
use App\Form\AdvertEditType;

/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
  /**
   * @Route("/{page}", name="advert_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
   */
  public function index($page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    $nbPerPage = 3;

    // Notre liste d'annonce en dur
    /*$listAdverts = array(
      array(
        'title'   => 'Recherche développpeur Symfony',
        'id'      => 1,
        'author'  => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Mission de webmaster',
        'id'      => 2,
        'author'  => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date'    => new \Datetime()),
      array(
        'title'   => 'Offre de stage webdesigner',
        'id'      => 3,
        'author'  => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date'    => new \Datetime())
    );*/

    $listAdverts = $this->getDoctrine()
        ->getManager()
        ->getRepository(Advert::class)
        ->getAdverts($page, $nbPerPage);

    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // Ici, on récupérera la liste des annonces, puis on la passera au template

    // Mais pour l'instant, on ne fait qu'appeler le template
    return $this->render('Advert/index.html.twig', [
      'listAdverts' => $listAdverts,
      'nbPages' => $nbPages,
      'page' => $page,
    ]);
  }

  /**
   * @Route("/view/{id}", name="advert_view", requirements={"id" = "\d+"})
   */
  public function view($id)
  {
    $em = $this->getDoctrine()->getManager();
    $advert = $em->getRepository(Advert::class)->find($id);

    // $advert est donc une instance de App\Entity\Advert
    // ou null si l'id $id  n'existe pas, d'où ce if :
    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On avait déjà récupéré la liste des candidatures
    $listApplications = $em
      ->getRepository(Application::class)
      ->findBy(array('advert' => $advert))
    ;

    // On récupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository(AdvertSkill::class)
      ->findBy(array('advert' => $advert))
    ;

    return $this->render('Advert/view.html.twig', array(
      'advert' => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  /**
   * @Route("/add", name="advert_add")
   */
  public function add(Request $request)
  {

    /*$advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
    $advert->setSlug("test");

    // On récupère toutes les compétences possibles
    $listSkills = $em->getRepository(Skill::class)->findAll();

    // Pour chaque compétence
    foreach ($listSkills as $skill) {
      // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
      $advertSkill = new AdvertSkill();

      // On la lie à l'annonce, qui est ici toujours la même
      $advertSkill->setAdvert($advert);
      // On la lie à la compétence, qui change ici dans la boucle foreach
      $advertSkill->setSkill($skill);

      // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
      $advertSkill->setLevel('Expert');

      // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
      $em->persist($advertSkill);*/

      $advert = new Advert();

      $form = $this->createForm(AdvertType::class, $advert);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          //$advert->getImage()->upload();
          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          return $this->redirectToRoute('advert_view', array('id' => $advert->getId()));
      }

    return $this->render('Advert/add.html.twig', [
        'form' => $form->createView(),
    ]);
  }


    // Création d'une première candidature
    /*$application1 = new Application();
    $application1->setAuthor('Marine');
    $application1->setContent("J'ai toutes les qualités requises.");

    // Création d'une deuxième candidature par exemple
    $application2 = new Application();
    $application2->setAuthor('Pierre');
    $application2->setContent("Je suis très motivé.");

    // On lie les candidatures à l'annonce
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);

    // Création de l'entité Image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');

    // On lie l'image à l'annonce
    $advert->setImage($image);

    // On peut ne pas définir ni la date ni la publication,
    // car ces attributs sont définis automatiquement dans le constructeur
    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :

    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();

    // Étape 1 : On « persiste » l'entité
    $em->persist($advert);

    // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    $em->persist($application1);
    $em->persist($application2);

    // Étape 2 : On « flush » tout ce qui a été persisté avant
    $em->flush();

    // Reste de la méthode qu'on avait déjà écrit
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // Puis on redirige vers la page de visualisation de cettte annonce
      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('Advert/add.html.twig', array('advert' => $advert));
  }*/

  /**
   * @Route("/edit/{id}", name="advert_edit", requirements={"id" = "\d+"})
   */
  public function edit($id, Request $request)
  {
    // Ici, on récupérera l'annonce correspondante à $id
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository(Advert::class)->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form = $this->createForm(AdvertEditType::class);

    $form->handleRequest($request);
    // Même mécanisme que pour l'ajout
    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();
      $this->addFlash('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('advert_view', array('id' => $advert->getId()));
    }

    /*$advert = array(
      'title'   => 'Recherche développpeur Symfony',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );*/
    return $this->render('Advert/edit.html.twig', array(
      'advert' => $advert,
      'form' => $form->createView()
    ));
  }

  /**
   * @Route("/delete/{id}", name="advert_delete", requirements={"id" = "\d+"})
   */
  public function delete($id)
  {
    $em = $this->getDoctrine()->getManager();
    // Ici, on récupérera l'annonce correspondant à $id
    $advert = $em->getRepository(Advert::class)->find($id);

    $form = $this->get('form.factory')->create();

    if ($form->isSubmitted() && $form->isValid())
    {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirectToRoute('advert_home');
    }
    // Ici, on gérera la suppression de l'annonce en question
    //if (null === $advert) {
    //  throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    //}

    // On boucle sur les catégories de l'annonce pour les supprimer
    //foreach ($advert->getCategories() as $category) {
    //  $advert->removeCategory($category);
    //}

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // On déclenche la modification


    return $this->render('Advert/delete.html.twig', [
        'advert' => $advert,
        'form' => $form->createView(),
    ]);
  }

  public function menuAction($limit)
  {
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
    /*$listAdverts = array(
      array('id' => 2, 'title' => 'Recherche développeur Symfony'),
      array('id' => 5, 'title' => 'Mission de webmaster'),
      array('id' => 9, 'title' => 'Offre de stage webdesigner')
    );*/

    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getRepository('Advert::class')->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On trie par date décroissante
      $limit,                  // On sélectionne $limit annonces
      0                        // À partir du premier
    );

    return $this->render('Advert/menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

  /**
   * Exemple de méthode pour modifier l'image d'une annonce
   */
  public function editImageAction($advertId)
  {
      $em = $this->getDoctrine()->getManager();

      // On récupère l'annonce
      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($advertId);

      // On modifie l'URL de l'image par exemple
      $advert->getImage()->setUrl('test.png');

      // On n'a pas besoin de persister l'annonce ni l'image.
      // Rappelez-vous, ces entités sont automatiquement persistées car
      // on les a récupérées depuis Doctrine lui-même

      // On déclenche la modification
      $em->flush();

      return new Response('OK');
    }

    /**
     * @Route("/traduction/{name}")
     */
    public function translationAction($name)
    {
      return $this->render('Advert/translation.html.twig', array(
        'name' => $name
      ));
    }
}
