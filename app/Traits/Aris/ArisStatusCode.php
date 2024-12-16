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
        if( strtolower( $status ) === "utilisé" || strtolower( $status ) === "utilise" ) {
            return "U";
        } else if ( strtolower( $status ) === "libre" ) {
            return "A";
        }
        return "";
    }
}
