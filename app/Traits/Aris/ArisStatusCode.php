<?php

namespace App\Traits\Aris;

trait ArisStatusCode
{
    public static function formatStatus(string $status): string
    {
        if( strtoupper( $status ) === "U") {
            return "Utilisé";
        } else if ( strtoupper( $status ) === "A" ) {
            return "Libre";
        }
        return "OCCUPE-ARIS";
    }

    public static function unFormatStatus(string $status) {
        if( strpos(strtolower( $status ), "utilisé") || strpos(strtolower( $status ), "utilise") ) {
            return "U";
        } else if ( strpos(strtolower( $status ), "libre") ) {
            return "A";
        }
        return "";
    }
}