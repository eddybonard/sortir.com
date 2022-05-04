<?php

namespace App\Controller\Services;

class Censurator
{
    /**
     * @param string $phrase
     * @return array|string|string[]
     */
    public function censure(string $phrase)
    {
        $nomDoiseau = array('enculer','salaud','salope','petasse','connard','nique','niquer','pute');

        $phrasePure = str_ireplace($nomDoiseau, '*', mb_strtolower($phrase));

        return $phrasePure;
    }
}