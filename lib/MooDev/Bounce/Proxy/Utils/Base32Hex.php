<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy\Utils;

/**
 * Class Base32Hex
 * Encodes stuff to base32hex.
 * @package MooDev\Bounce\Proxy\Utils
 */
class Base32Hex {

    /**
     * Encode to base32. Note that we don't bother adding padding.
     * @param $str String to encode.
     * @return string Base32 encoded string.
     */
    public static function encode($str) {
        $bitWorkArea = 0;
        $out = "";
        $mask = 0x1f;
        $numBits = 0;
        foreach (str_split($str) as $chr) {
            $b = ord($chr);
            // Doesn't matter if we overflow max int, we've already added the high bits to $out, and on shifting past
            // max int they just get discarded.
            $bitWorkArea = ($bitWorkArea << 8) + $b;
            $numBits += 8; // We've just added 8 bits to the work area (duh)
            while ($numBits >= 5) {
                // There are enough bits for us to add something to the output.
                $out .= base_convert(($bitWorkArea >> $numBits-5) & $mask, 10, 32);
                $numBits -= 5; // And now we can forget about the high 5 bits.
            }
        }
        if ($numBits > 0) {
            // OK, we've got less than 5 bits. Pad with 0 and do the mafs.
            $out .= base_convert(($bitWorkArea << 5 - $numBits) & $mask, 10, 32);
        }
        return $out;
    }

}