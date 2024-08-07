<?php

namespace App\Controller;

use App\Entity\Portefeuille;
use App\Form\PortefeuilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class CompteController extends AbstractController
{
    // METHODE POUR CREER UN PORTEFEUILLE
    #[Route('/creePortefeuille', name: 'app_ajouter_portefeuille')]
    public function ajouterPortefeuille(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $portefeuille = new Portefeuille();
        $form = $this->createForm(PortefeuilleType::class, $portefeuille);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification supplémentaire pour l'email
            $email = $portefeuille->getEmail();
            if (!preg_match('/@gmail\.com$/', $email)) {
                $this->addFlash('error', 'L\'email doit se terminer par @gmail.com');
                return $this->redirectToRoute('app_ajouter_portefeuille');
            }

            // Vérification des champs obligatoires
            $fullName = $portefeuille->getFullName();
            $phoneNumber = $portefeuille->getPhoneNumber();
            $password = $portefeuille->getPassword();
            $userType = $portefeuille->getUserType();

            if (empty($fullName) || empty($phoneNumber) || empty($password) || empty($userType)) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('app_ajouter_portefeuille');
            }

            // Validation du numéro de téléphone (chiffres seulement)
            if (!ctype_digit($phoneNumber)) {
                $this->addFlash('error', 'Le numéro de téléphone ne doit contenir que des chiffres.');
                return $this->redirectToRoute('app_ajouter_portefeuille');
            }

            // Validation des entités via Validator
            $errors = $validator->validate($portefeuille);
            if (count($errors) > 0) {
                $this->addFlash('error', (string) $errors);
                return $this->redirectToRoute('app_ajouter_portefeuille');
            }

            // Traitement de l'upload de la photo d'identité
            $photoIdentiteFile = $form->get('downloadURL')->getData();
            if ($photoIdentiteFile) {
                $newFilename = uniqid() . '.' . $photoIdentiteFile->guessExtension();

                try {
                    $photoIdentiteFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement de la photo d\'identité.');
                    return $this->redirectToRoute('app_ajouter_portefeuille');
                }

                // Met à jour le champ 'downloadURL' de l'entité Portefeuille avec le nom du fichier
                $portefeuille->setDownloadURL($newFilename);
            }

            // Enregistrement en base de données
            $entityManager->persist($portefeuille);
            $entityManager->flush();

            $this->addFlash('success', 'Portefeuille créé avec succès.');

            return $this->redirectToRoute('app_afficher_portefeuille');
        }

        return $this->render('compte/creer_compte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // METHODE POUR MODIFIER UN PORTEFEUILLE
    #[Route('/compte/{id}/modifierPortefeuille', name: 'app_modifier_portefeuille')]
    public function modifierPortefeuille(
        Request $request,
        Portefeuille $portefeuille,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        ValidatorInterface $validator
    ): Response {
        $form = $this->createForm(PortefeuilleType::class, $portefeuille);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation supplémentaire pour l'email
            $email = $portefeuille->getEmail();
            if (!preg_match('/@gmail\.com$/', $email)) {
                $this->addFlash('error', 'L\'email doit se terminer par @gmail.com');
                return $this->redirectToRoute('app_modifier_portefeuille', ['id' => $portefeuille->getId()]);
            }

            // Vérification des champs obligatoires
            $fullName = $portefeuille->getFullName();
            $phoneNumber = $portefeuille->getPhoneNumber();
            $password = $portefeuille->getPassword();
            $userType = $portefeuille->getUserType();

            if (empty($fullName) || empty($phoneNumber) || empty($password) || empty($userType)) {
                $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
                return $this->redirectToRoute('app_modifier_portefeuille', ['id' => $portefeuille->getId()]);
            }

            // Validation du numéro de téléphone (chiffres seulement)
            if (!ctype_digit($phoneNumber)) {
                $this->addFlash('error', 'Le numéro de téléphone ne doit contenir que des chiffres.');
                return $this->redirectToRoute('app_modifier_portefeuille', ['id' => $portefeuille->getId()]);
            }

            // Validation des entités via Validator
            $errors = $validator->validate($portefeuille);
            if (count($errors) > 0) {
                $this->addFlash('error', (string) $errors);
                return $this->redirectToRoute('app_modifier_portefeuille', ['id' => $portefeuille->getId()]);
            }

            // Traitement de l'upload de la photo d'identité
            $photoIdentiteFile = $form->get('downloadURL')->getData();
            if ($photoIdentiteFile) {
                $newFilename = uniqid() . '.' . $photoIdentiteFile->guessExtension();

                try {
                    $photoIdentiteFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement de la photo d\'identité.');
                    return $this->redirectToRoute('app_modifier_portefeuille', ['id' => $portefeuille->getId()]);
                }

                // Met à jour le champ 'downloadURL' de l'entité Portefeuille avec le nom du fichier
                $portefeuille->setDownloadURL($newFilename);
            }

            // Enregistrement des modifications
            $entityManager->flush();

            $this->addFlash('success', 'Portefeuille modifié avec succès.');

            return $this->redirectToRoute('app_afficher_portefeuille');
        }

        return $this->render('compte/modifier_compte.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // METHODE POUR AFFICHER LES PORTEFEUILLES
    #[Route('/afficherPortefeuille', name: 'app_afficher_portefeuille')]
    public function afficherPortefeuille(EntityManagerInterface $entityManager): Response
    {
        $portefeuilles = $entityManager->getRepository(Portefeuille::class)->findAll();

        return $this->render('compte/afficher_compte.html.twig', [
            'portefeuilles' => $portefeuilles,
        ]);
    }

    // METHODE POUR AFFICHER LES INFOS JURIDIQUES DU PORTEFEUILLE
    #[Route('/infoIdentite', name: 'app_info_identite')]
    public function infoIdentite(EntityManagerInterface $entityManager): Response
    {
        $portefeuilles = $entityManager->getRepository(Portefeuille::class)->findAll();

        return $this->render('compte/info_identite.html.twig', [
            'portefeuilles' => $portefeuilles,
        ]);
    }

    // METHODE POUR SUPPRIMER UN PORTEFEUILLE
    #[Route('/compte/{id}/supprimerPortefeuille', name: 'app_supprimer_portefeuille', methods: ['POST'])]
    public function supprimerPortefeuille(Request $request, Portefeuille $portefeuille, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $portefeuille->getId(), $request->request->get('_token'))) {
            $entityManager->remove($portefeuille);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_afficher_portefeuille');
    }

    // METHODE POUR IMPRIMER UN PORTEFEUILLE 
    #[Route('/compte/{id}/imprimer', name: 'app_imprimer_portefeuille')]
    public function imprimerPortefeuille(Portefeuille $portefeuille): Response
    {
        // Configuration des options de Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial'); // Définir la police par défaut
        $pdfOptions->set('isRemoteEnabled', true); // Activer l'accès aux fichiers distants
        $pdfOptions->set('isHtml5ParserEnabled', true); // Activer l'analyseur HTML5
        $pdfOptions->set('isFontSubsettingEnabled', true); // Activer le sous-ensemble de polices

        // Instancier Dompdf avec nos options
        $dompdf = new Dompdf($pdfOptions);

        // Récupérer le HTML généré dans notre fichier Twig
        $html = $this->renderView('compte/imprimer_portefeuille.html.twig', [
            'portefeuille' => $portefeuille
        ]);

        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);

        // (Optionnel) Définir le format de papier et l'orientation (portrait ou paysage)
        $dompdf->setPaper('A4', 'portrait');

        // Rendre le HTML en PDF
        $dompdf->render();

        // Sortie du PDF généré dans le navigateur (téléchargement forcé)
        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="portefeuille.pdf"',
        ]);
    }
}
