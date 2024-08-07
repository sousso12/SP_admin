<?php

namespace App\Controller;

use App\Entity\Transactions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Portefeuille;

class TransactionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // METHODE POUR AFFICHER LA LISTE DES TRANSACTIONS
    #[Route('/consulter_historique', name: 'app_transaction')]
    public function historique(): Response
    {
        $transactions = $this->entityManager->getRepository(Transactions::class)->findAll();

        return $this->render('transaction/historique.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    // METHODE POUR SUPPRIMER UNE TRANSACTION
    #[Route('/supprimer_transaction/{id}', name: 'app_supprimer_transaction')]
    public function supprimerTransaction(int $id): Response
    {
        $transaction = $this->entityManager->getRepository(Transactions::class)->find($id);

        if ($transaction) {
            $this->entityManager->remove($transaction);
            $this->entityManager->flush();

            $this->addFlash('success', 'La transaction a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'La transaction n\'a pas été trouvée.');
        }

        return $this->redirectToRoute('app_transaction');
    }

    // METHODE POUR REJETER UNE TRANSACTION
    #[Route('/rejeter_transaction/{numTransaction}', name: 'app_rejeter_transaction')]
    public function rejeterTransaction(int $numTransaction): Response
    {
        $transactionRepository = $this->entityManager->getRepository(Transactions::class);
        $portefeuilleRepository = $this->entityManager->getRepository(Portefeuille::class);

        // Récupérer la transaction à rejeter
        $transaction = $transactionRepository->find($numTransaction);

        if (!$transaction) {
            $this->addFlash('error', 'Transaction non trouvée');
            return $this->redirectToRoute('app_transaction');
        }

        // Récupérer les informations nécessaires de la transaction
        $amount = $transaction->getAmount();
        $numCompteClient = $transaction->getNumCompteClient();
        $numCompteMarchand = $transaction->getNumCompteMarchand();

        // Convertir le montant en nombre
        $cleanedAmount = floatval(preg_replace('/[^0-9.-]/', '', $amount));

        if (is_nan($cleanedAmount)) {
            $this->addFlash('error', 'Montant invalide');
            return $this->redirectToRoute('app_transaction');
        }

        // Démarrer une transaction pour garantir l'atomicité des opérations
        $this->entityManager->beginTransaction();

        try {
            // Créditer le compte client
            $client = $portefeuilleRepository->findOneBy(['numCompte' => $numCompteClient]);
            if (!$client) {
                throw new \Exception('Compte client non trouvé');
            }
            $client->setSolde($client->getSolde() + $cleanedAmount);

            // Débiter le compte marchand
            $marchand = $portefeuilleRepository->findOneBy(['numCompte' => $numCompteMarchand]);
            if (!$marchand) {
                throw new \Exception('Compte marchand non trouvé');
            }
            $marchand->setSolde($marchand->getSolde() - $cleanedAmount);

            // Mettre à jour le statut de la transaction
            $transaction->setStatus('Rejetée');

            // Persister les changements
            $this->entityManager->persist($client);
            $this->entityManager->persist($marchand);
            $this->entityManager->persist($transaction);

            // Valider la transaction
            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->addFlash('success', 'Transaction rejetée avec succès');
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->addFlash('error', 'Erreur lors du rejet de la transaction : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_transaction');
    }
}
