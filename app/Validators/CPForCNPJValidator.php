<?php

namespace App\Validators;

class CPForCNPJValidator
{
    /**
     * Validando se é um cpf ou cnpj
     * @param mixed $document
     * @return boolean
     * 
     */
    public static function validateDocument($document)
    {
        // Remove special characters and whitespace
        $document = preg_replace('/[^0-9]/', '', $document);

        // Check if it is a CPF or CNPJ based on the string length
        $length = strlen($document);
        if ($length === 11) {
            // CPF
            // Check the verification digits
            if (preg_match('/(\d)\1{10}/', $document)) {
                return false; // CPF with all digits equal is invalid
            }
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += intval($document[$i]) * (10 - $i);
            }
            $remainder = $sum % 11;
            if ($remainder < 2) {
                $verificationDigit1 = 0;
            } else {
                $verificationDigit1 = 11 - $remainder;
            }
            if ($verificationDigit1 != intval($document[9])) {
                return false; // Invalid verification digit 1
            }
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum += intval($document[$i]) * (11 - $i);
            }
            $remainder = $sum % 11;
            if ($remainder < 2) {
                $verificationDigit2 = 0;
            } else {
                $verificationDigit2 = 11 - $remainder;
            }
            if ($verificationDigit2 != intval($document[10])) {
                return false; // Invalid verification digit 2
            }
            return true; // Valid CPF
        } elseif ($length === 14) {
            // CNPJ
            // Check the verification digits
            if (preg_match('/(\d)\1{13}/', $document)) {
                return false; // CNPJ with all digits equal is invalid
            }
            $sum = 0;
            $multiplier = 5;
            for ($i = 0; $i < 12; $i++) {
                $sum += intval($document[$i]) * $multiplier;
                $multiplier = ($multiplier === 2) ? 9 : $multiplier - 1;
            }
            $remainder = $sum % 11;
            $verificationDigit1 = ($remainder < 2) ? 0 : 11 - $remainder;
            if ($verificationDigit1 != intval($document[12])) {
                return false; // Invalid verification digit 1
            }
            $sum = 0;
            $multiplier = 6;
            for ($i = 0; $i < 13; $i++) {
                $sum += intval($document[$i]) * $multiplier;
                $multiplier = ($multiplier === 2) ? 9 : $multiplier - 1;
            }
            $remainder = $sum % 11;
            $verificationDigit2 = ($remainder < 2) ? 0 : 11 - $remainder;
            if ($verificationDigit2 != intval($document[13])) {
                return false; // Invalid verification digit 2
            }
            return true; // Valid CNPJ
        } else {
            return false; // Invalid length
        }
    }
}
