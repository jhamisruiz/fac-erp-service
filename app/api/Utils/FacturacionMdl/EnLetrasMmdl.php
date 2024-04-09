<?php

namespace App\Utils\FacturacionMdl;

class EnLetrasMmdl
{
    private const VOID = "";
    private const SP = " ";
    private const DOT = ".";
    private const ZERO = "0";
    private const NEG = "Menos";

    public function valorEnLetras($x, $moneda)
    {
        $s = "";
        $ent = "";
        $frc = "";
        $signo = "";
        if (floatVal($x) < 0) {
            $signo = self::NEG . " ";
        } else {
            $signo = "";
        }
        if (intval(number_format($x, 2, '.', '')) != $x) {
            $s = number_format($x, 2, '.', '');
        } else {
            $s = number_format($x, 2, '.', '');
        }

        $pto = strpos($s, self::DOT);

        if ($pto === false) {
            $ent = $s;
            $frc = self::VOID;
        } else {
            $ent = substr($s, 0, $pto);
            $frc = substr($s, $pto + 1);
        }

        if ($ent == self::ZERO || $ent == self::VOID) {
            $s = "CERO ";
        } elseif (strlen($ent) > 7) {
            $s = $this->subValLetra(intval(substr($ent, 0, strlen($ent) - 6))) .
                "MILLONES " . $this->subValLetra(intval(substr($ent, -6, 6)));
        } else {
            $s = $this->subValLetra(intval($ent));
        }

        if (substr($s, -9, 9) == "MILLONES " || substr($s, -7, 7) == "MILLÓN ") {
            $s = $s . "DE ";
        }

        $s = $s;

        if ($frc !== self::VOID) {
            $s = $s . " CON " . $frc . "/100";
        }
        $letras = $signo . $s . " " . $moneda;
        return (string)$letras;
    }

    private function subValLetra($numero)
    {
        $rtn = '';
        $tem = '';

        $x = trim("$numero");
        $n = strlen($x);

        $tem = self::VOID;

        for ($i = $n; $i > 0; $i--) {
            $tem = $this->parte(intval(substr($x, $n - $i, 1) . str_repeat(self::ZERO, $i - 1)));
            if ($tem !== "CERO") {
                $rtn .= $tem . self::SP;
            }
        }

        $rtn = str_replace(" MIL MIL", " UN MIL", $rtn);

        $ptr = -1;
        do {
            $ptr = strpos($rtn, "CIEN ", $ptr + 1);
            if ($ptr !== false) {
                $tem = substr($rtn, $ptr + 5, 1);
                if ($tem == "M" || $tem == self::VOID) {
                    // no hacer nada
                } else {
                    $this->replaceStringFrom($rtn, "CIEN", "CIENTO", $ptr);
                }
            }
        } while ($ptr !== false);

        $rtn = str_replace("DIEZ UNO", "ONCE", $rtn);
        $rtn = str_replace("DIEZ DOS", "DOCE", $rtn);
        $rtn = str_replace("DIEZ TRES", "TRECE", $rtn);
        $rtn = str_replace("DIEZ CUATRO", "CATORCE", $rtn);
        $rtn = str_replace("DIEZ CINCO", "QUINCE", $rtn);
        $rtn = str_replace("DIEZ SEIS", "DIECISEIS", $rtn);
        $rtn = str_replace("DIEZ SIETE", "DIECISIETE", $rtn);
        $rtn = str_replace("DIEZ OCHO", "DIECIOCHO", $rtn);
        $rtn = str_replace("DIEZ NUEVE", "DIECINUEVE", $rtn);
        $rtn = str_replace("VEINTE UN", "VEINTIUN", $rtn);
        $rtn = str_replace("VEINTE DOS", "VEINTIDOS", $rtn);
        $rtn = str_replace("VEINTE TRES", "VEINTITRES", $rtn);
        $rtn = str_replace("VEINTE CUATRO", "VEINTICUATRO", $rtn);
        $rtn = str_replace("VEINTE CINCO", "VEINTICINCO", $rtn);
        $rtn = str_replace("VEINTE SEIS", "VEINTISEIS", $rtn);
        $rtn = str_replace("VEINTE SIETE", "VEINTISIETE", $rtn);
        $rtn = str_replace("VEINTE OCHO", "VEINTIOCHO", $rtn);
        $rtn = str_replace("VEINTE NUEVE", "VEINTINUEVE", $rtn);

        if (substr($rtn, 0, 1) == "M") {
            $rtn = " " . $rtn;
        }

        for ($i = 65; $i <= 88; $i++) {
            if ($i !== 77) {
                $rtn = str_replace("A " . chr($i), "* Y " . chr($i), $rtn);
            }
        }

        $rtn = str_replace("*", "A", $rtn);

        return $rtn;
    }

    private function replaceStringFrom(&$x, $oldWrd, $newWrd, $ptr)
    {
        $x = substr($x, 0, $ptr) . $newWrd . substr($x, strlen($oldWrd) + $ptr);
    }

    private function parte($x)
    {
        $rtn = '';
        $t = '';
        $i = 0;

        do {
            switch ($x) {
                case 0:
                    $t = "CERO";
                    break;
                case 1:
                    $t = "UNO";
                    break;
                case 2:
                    $t = "DOS";
                    break;
                case 3:
                    $t = "TRES";
                    break;
                case 4:
                    $t = "CUATRO";
                    break;
                case 5:
                    $t = "CINCO";
                    break;
                case 6:
                    $t = "SEIS";
                    break;
                case 7:
                    $t = "SIETE";
                    break;
                case 8:
                    $t = "OCHO";
                    break;
                case 9:
                    $t = "NUEVE";
                    break;
                case 10:
                    $t = "DIEZ";
                    break;
                case 20:
                    $t = "VEINTE";
                    break;
                case 30:
                    $t = "TREINTA";
                    break;
                case 40:
                    $t = "CUARENTA";
                    break;
                case 50:
                    $t = "CINCUENTA";
                    break;
                case 60:
                    $t = "SESENTA";
                    break;
                case 70:
                    $t = "SETENTA";
                    break;
                case 80:
                    $t = "OCHENTA";
                    break;
                case 90:
                    $t = "NOVENTA";
                    break;
                case 100:
                    $t = "CIEN";
                    break;
                case 200:
                    $t = "DOSCIENTOS";
                    break;
                case 300:
                    $t = "TRESCIENTOS";
                    break;
                case 400:
                    $t = "CUATROCIENTOS";
                    break;
                case 500:
                    $t = "QUINIENTOS";
                    break;
                case 600:
                    $t = "SEISCIENTOS";
                    break;
                case 700:
                    $t = "SETECIENTOS";
                    break;
                case 800:
                    $t = "OCHOCIENTOS";
                    break;
                case 900:
                    $t = "NOVECIENTOS";
                    break;
                case 1000:
                    $t = "MIL";
                    break;
                case 1000000:
                    $t = "MILLÓN";
                    break;
            }

            if ($t == self::VOID) {
                $i++;
                $x = $x / 1000;
                if ($x == 0) $i = 0;
            } else {
                break;
            }
        } while ($i != 0);

        $rtn = $t;
        switch ($i) {
            case 0:
                $t = self::VOID;
                break;
            case 1:
                $t = " MIL";
                break;
            case 2:
                $t = " MILLONES";
                break;
            case 3:
                $t = " BILLONES";
                break;
        }

        return $rtn . $t;
    }
}
