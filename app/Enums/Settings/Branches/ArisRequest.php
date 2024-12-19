<?php

namespace App\Enums\Settings\Branches;

use App\Enums\Settings\SettingNode;

/**
 * Class Treatment. Raw ArisStatusRequest settings
 * @package App\Enums\Settings\Branches
 *
 * @method activate()
 * @method max_esims()
 * @method max_running()
 * @method max_retries()
 * @method max_waiting()
 * @method formatcolumns()
 */
class ArisRequest extends SettingNode
{
    public function __construct()
    {
        parent::__construct("arisrequest",null,null,null,null,"settings Aris Requests.");

        $this->addChild("activate", "1", "bool", "active ou desactive les requêtes ARIS.");
        $this->addChild("formatcolumns", null, null, "Formattage des colonnes.")
            ->addChild("append_batch_max", "10", "integer", "nombre max de colonnes a ajouter au batch de Requête de la ligne.");

        $max_esims = $this->addChild("max_esims", null, null, "Nombre Max d Esims autorise par code de Requête.");
        $max_esims->addChild("status", "10", "integer", "nombre max d Esims de Requêtes de Statut.");

        $max_running = $this->addChild("max_running", null, null, "Nombre Max d execution autorise par code de Requête.");
        $max_running->addChild("status", "10", "integer", "nombre max d executions de Requêtes de Statut.");

        $max_retries = $this->addChild("max_retries", "10", "integer", "nombre max de tentatives de Requête.");
        $max_retries->addChild("status", "10", "integer", "nombre max de tentatives de Requêtes de Statut.");

        $max_retries = $this->addChild("max_waiting", "10", "integer", "nombre max de Requêtes en attente.");
        $max_retries->addChild("status", "10", "integer", "nombre max de Requêtes de Statut en attente.");
    }
}
