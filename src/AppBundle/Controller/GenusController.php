<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Entity\SubFamily;
use AppBundle\Service\MarkdownTransformer;
use AppBundle\Service\SlowGenusGenerator;
use AppBundle\Service\SubFamilyAssigner;
use AppBundle\Service\SubFamilyGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\User;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.rand(1, 100));
        $genus->setSpeciesCount(rand(100, 99999));

        /** @var SubFamilyGenerator $generate_subfamilies */
        $generate_subfamilies = $this->get('app.generate_subfamilies');
        $generate_subfamilies->setDoctrine($this->getDoctrine());
        $generate_subfamilies->generateSubFamilies('https://en.wikipedia.org/wiki/Octopus_(genus)', 'Octopus');
        /** @var SubFamilyAssigner $assign_serv */
        $assign_serv = $this->get('app.assign_subfamily');
        $assign_serv->setDoctrine($this->getDoctrine());
        /** @var SubFamily $randomSubFamily */
        $randomSubFamily = $assign_serv->assignSubFamily();
        $genus->setSubFamily($randomSubFamily);
        $genus->setFirstDiscoveredAt(new \DateTime());

        $genusNote = new GenusNote();
        $genusNote->setUsername('AquaWeaver');
        $genusNote->setUserAvatarFilename('ryan.jpeg');
        $genusNote->setNote('I counted 8 legs... as they wrapped around me');
        $genusNote->setCreatedAt(new \DateTime('-1 month'));
        $genusNote->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genusNote);

        $scientist = new User();
        $scientist->setName("Dr. Jimmy");
        $scientist->setIsScientist(true);
        $scientist->setSlug('dr-jimmy');
        $em->persist($scientist);

        $genus->addScientist($scientist);

        $em->persist($genus);
        $em->flush();

        return new Response('<html><body>Genus created!</body></html>');
    }

    /**
     * @Route("/genus/generate")
     */
    public function generateAction()
    {
        $url = 'https://en.wikipedia.org/wiki/Octopus_(genus)';
        $needle = 'Octopus';

        /** @var SubFamilyGenerator $service */
        $gen = $this->get('app.generate_subfamilies');
        $gen->setDoctrine($this->getDoctrine());
        $gen->generateSubFamilies($url, $needle);
        return new Response('<html><body>SubFamilies generated from</body></html>' . $url);
    }

    /**
     * @Route("/genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Genus[] $genuses */
        $genuses = $em->getpository('AppBundle:Genus')
            ->findAllPublishedOrderedByRecentlyActive();


        return  new JsonResponse("altceva");
        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses
        ]);
    }

    /**
     * @Route("/genus/{slug}", name="genus_show")
     */
    public function showAction(Genus $genus)
    {
        $em = $this->getDoctrine()->getManager();
        $markdownTransformer = $this->get('app.markdown_transformer');
        $funFact = $markdownTransformer->parse($genus->getFunFact());

        $this->get('logger')
            ->info('Showing genus: '.$genus->getName());

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' => count($recentNotes)
        ));
    }

    /**
     * @Route("/scientist/{slug}", name="scientist_show")
     */
    public function scientistAction(User $scientist)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('scientist/show.html.twig', array(
            'scientist' => $scientist,
        ));
    }

    /**
     * @Route("/genus/{slug}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {
        $notes = [];

        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }

        $data = [
            'notes' => $notes
        ];

        return new JsonResponse($data);
    }
}
