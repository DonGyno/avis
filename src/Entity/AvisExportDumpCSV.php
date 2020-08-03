<?php


namespace App\Entity;


class AvisExportDumpCSV
{
    public function getFileExtension() { return 'csv'; }
    public function getContentType()   { return 'text/csv'; }

    public function dump($avis)
    {
        $fp = fopen('php://output','w+');

        // Header
        fputcsv($fp, array(
            'Id', 'Nom', 'Prénom', 'Email', 'Nom Entreprise Concernée', 'Date envoi enquête', 'Date réponse enquête', 'Note Prestation Réalisée', 'Note Professionnalisme Entreprise', 'Note Satisfaction Globale', 'Commentaire/Remarque prestation', 'Souhaite un témoignage vidéo ?', 'Téléphone',
        ),';');

        // Games is passed in
        foreach($avis as $avi)
        {
            // Build up row
            fputcsv($fp, array(
                $avi->getId(), $avi->getNomDestinataire(), $avi->getPrenomDestinataire(), $avi->getEmailDestinataire(), $avi->getEntrepriseConcernee()->getNom(), $avi->getDateEnvoiEnquete()->format('d/m/Y H:i'), $avi->getDateReponseEnquete()->format('d/m/Y H:i'), $avi->getNotePrestationRealisee(), $avi->getNoteProfessionnalismeEntreprise(), $avi->getNoteSatisfactionGlobale(), $avi->getRecommanderCommentaireAEntreprise(), $avi->getTemoignageVideo(), $avi->getTelephoneDestinataire(),
            ),';');
        }
        // Return the content
        //rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        return $csv;
    }
}