<?php

namespace App\Http\Requests\Employe;

use App\Models\Employes\Employe;
use App\Models\Employes\Departement;
use App\Models\Employes\FonctionEmploye;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EmployeRequest
 * @package App\Http\Requests\Employe
 *
 * @property string $nom
 * @property string|null $matricule
 * @property string|null $prenom
 * @property string|null $nom_complet
 * @property string|null $adresse
 * @property string|null $objectguid
 * @property string|null $thumbnailphoto
 *
 * @property FonctionEmploye $fonction
 * @property Departement $departement
 * @property Departement $departements_responsable
 */
class EmployeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Employe::defaultRules();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return Employe::messagesRules();
    }
}
