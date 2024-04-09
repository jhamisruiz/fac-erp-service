<?php

namespace App\Utils;

class Validations
{
    public function replace_invalid_chars($cadena, $obviarcaracteres = [])
    {
        // Lista de caracteres a reemplazar
        $caracteres_a_reemplazar = [
            "'", "#", "$", "%", "&", "(", ")", "*", "+", "-", ".", "/", "<", "=", ">", "?", "@", "[", "\\",
            "]", "^", "_", "`", "{", "|", "}", "~", "¡", "¢", "£", "¤", "¥", "¦", "§", "¨", "©", "ª", "«",
            "¬", "®", "°", "±", "²", "³", "´", "µ", "¶", "·", "¸", "¹", "º", "»", "¼", "½", "¾", "¿", "À",
            "Á", "Â", "Ã", "Ä", "Å", "Æ", "Ç", "È", "É", "Ê", "Ë", "Ì", "Í", "Î", "Ï", "Ð", "Ñ", "Ò", "Ó",
            "Ô", "Õ", "Ö", "×", "Ø", "Ù", "Ú", "Û", "Ü", "Ý", "Þ", "ß", "à", "á", "â", "ã", "ä", "å", "æ",
            "ç", "è", "é", "ê", "ë", "ì", "í", "î", "ï", "ð", "ñ", "ò", "ó", "ô", "õ", "ö", "÷", "ø", "ù",
            "ú", "û", "ü", "ý", "þ", "ÿ", "Œ", "œ", "Š", "š", "Ÿ", "ƒ", "–", "—", "‘", "’", "‚", "“", "”",
            "„", "†", "‡", "•", "…", "‰", "€", "™"
        ];

        // Reemplazar solo si el caracter no está en la lista de obviar
        foreach ($caracteres_a_reemplazar as $caracter) {
            if (!in_array($caracter, $obviarcaracteres)) {
                $cadena = str_replace($caracter, "", $cadena);
            }
        }

        return $cadena;
    }
}
