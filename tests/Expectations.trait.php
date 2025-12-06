<?php

trait Expectations {

    protected function getExpected($name) {
        switch ($name) {
        case 'zoek_medicatielijst_werknummer':
            return [
                ['schaapId' => 1, 'werknr' => 'KZA'],
                ['schaapId' => 2, 'werknr' => 'KZB'],
                ['schaapId' => 3, 'werknr' => 'KZC'],
            ];
        case 'zoek_medicatie_lijst':
            return [
                ['schaapId' => 1, 'levensnummer' => 'KZA'],
                ['schaapId' => 2, 'levensnummer' => 'KZB'],
                ['schaapId' => 3, 'levensnummer' => 'KZC'],
            ];
        case 'zoek_schaapgegevens':
            return [
                [
                    'schaapId' => 4,
                    'levensnummer' => '4',
                    'werknr' => '4',
                    'dmgeb' => null, // niet gebruikt in MedRegistratie
                    'gebdm' => null,
                    'geslacht' => 'ram',
                    'aanw' => null,
                    'hoknr' => null,
                    'lstgeblam' => null,
                    'generatie' => 'lam', // niet gebruikt in MedRegistratie
                    'actId' => '1',
                    'af' => '0',
                ],
            ];
        case 'zoek_schaap_aflever':
            return [
                [
                    'schaapId' => 4,
                    'levensnummer' => '131072',
                    'werknr' => '31072',
                    'kg' => 1,
                ]
            ];
        }
    }

}
