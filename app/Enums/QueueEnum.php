<?php

namespace App\Enums;

use App\Traits\Enum\EnumTrait;
use App\Enums\Attributes\Description;

enum QueueEnum: string
{
    use EnumTrait;

    #[Description('System LOG')]
    case SYSTEMLOG = 'systemlog';

    #[Description('Execution Trace')]
    case EXECTRACE = 'exectrace';

    #[Description('Update Progression')]
    case PROGRESSION = 'progression';

    #[Description('Execution de Listeners')]
    case LISTENER = 'listener';

    #[Description('Traitement Principal')]
    case MAIN = 'main';


    #[Description('Traitement des Requetes de Statut ARIS')]
    case ARISSTATUSREQUEST = 'aris_status_request';

    #[Description('Traitement ESIM restees longtemps en attribution')]
    case ESIMLONGATTRIBUTION = 'esim_long_attribution';

    #[Description('Regule les ESIM en status NOUVEAU')]
    case ESIMSTATUSNEWREGULATION = 'esim_status_new_regulation';

    #[Description('Importation Lignes Fichier')]
    case IMPORTFILE = 'importfile';

    #[Description('Formattage de Fichier')]
    case FORMATFILE = 'formatfile';

    #[Description('Merge des Lignes formatees de Fichier')]
    case MERGEFILE = 'mergefile';
}
