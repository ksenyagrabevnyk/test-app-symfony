<?php

namespace AdminBundle\Helper;

class GenerationUUIDHelper
{
    public static function uuid($type = null)
    {
        return is_null($type) ?
            sprintf( "%04x%04x-%05X-%04x-%04x%04x%04x",
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

                // 16 bits for "time_hi_and_version",
                // five most significant bits holds version number 5
                mt_rand( 0, 0x7ffff ) | 0x5000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,

                // 48 bits for "node"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            )
            :  sprintf( $type . "-%04x-%05X-%04x-%04x%04x%04x",
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ),

                // 16 bits for "time_hi_and_version",
                // five most significant bits holds version number 5
                mt_rand( 0, 0x7ffff ) | 0x5000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,

                // 48 bits for "node"
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            )
        ;
    }
}
