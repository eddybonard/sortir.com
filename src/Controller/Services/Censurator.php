<?php


namespace App\Controller\Services;


class Censurator
{
    public function censure(string $phrase)
    {
        $nomDoiseau = array('enculer','salaud','salope','petasse','connard','nique','niquer','pute');

        $phrasePure = str_ireplace($nomDoiseau, '*', $phrase);

        return $phrasePure;
    }
}