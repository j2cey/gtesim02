<?php

namespace App\Models\HowTos;

use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use App\Contracts\IsBaseModel;
use App\Traits\Comment\HasComments;
use App\Contracts\Comments\ICommentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class HowToStep
 * @package App\Models\HowTos
 *
 * @property integer $id
 *
 * @property string $uuid
 * @property bool $is_default
 * @property int|null $status_id status reference
 *
 * @property string $title
 * @property integer $posi
 * @property string $description
 * @property int|null $how_to_id how_to reference
 * @property int|null $how_to_thread_id how_to_thread reference
 *
 * @property integer|null $created_by
 * @property integer|null $updated_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property HowToThread $howtothread
 * @property HowTo $howto
 */

class HowToStep extends BaseModel implements ICommentable, IsBaseModel
{
    use HasComments, HasFactory, \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $with = ['tags','howto'];

    #region Validation Rules

    public static function defaultRules() {
        return [
            'title' => ['required'],
            'howto' => ['required']
        ];
    }
    public static function createRules() {
        return array_merge(self::defaultRules(), [

        ]);
    }
    public static function updateRules($model) {
        return array_merge(self::defaultRules(), [

        ]);
    }

    public static function messagesRules() {
        return [
            'title.required' => 'The Title is required',
            'howto.required' => 'The Section (HTML) is required',
        ];
    }

    #endregion

    #region Eloquent Relationships

    public function howtothread() {
        return $this->belongsTo(HowToThread::class, 'how_to_thread_id');
    }

    public function howto() {
        return $this->belongsTo(HowTo::class, 'how_to_id');
    }

    #endregion

    #region Custom Functions

    public static function createNew(HowToThread $howtothread, HowTo $howto, $posi, $title = null, $description = null, $tags = null) : HowToStep
    {
        $step_title = is_null($title) ? $howto->title : $title;

        // Move steps down if any
        $howtothread->shiftStepsDownFrom($posi);

        $howtostep = self::create([
            'title' => $step_title,
            'posi' => $posi,
            'description' => $description,
        ]);

        $howtostep->howtothread()->associate($howtothread);
        $howtostep->howto()->associate($howto);


        if ( ! is_null($tags)) {
            $howtostep->syncTags($tags);
        }

        $howtostep->save();

        return $howtostep;
    }

    public function updateOne(HowTo $howto, $title, $posi, $description, $tags = null) : HowToStep
    {
        // Move steps if any must change
        if ($this->posi !== $posi) {
            //dd("posis differs", $this, $title, $posi);
            if ($posi > $this->howtothread->steps()->count()) {
                // the current step will be the last
                $posi = $this->howtothread->steps()->count();
                // we move up all others steps till the current step position's
                $this->howtothread->shiftStepsUpTo($this->posi);
            } else {
                // we move steps down
                $this->howtothread->shiftStepsDownFrom($posi);
            }
        }
        //dd("after if", $this, $title, $posi);
        $this->update([
            'title' => $title,
            'posi' => $posi,
            'description' => $description,
        ]);

        $this->howto()->associate($howto);

        if ( ! is_null($tags)) {
            $this->syncTags($tags);
        }

        $this->save();

        return $this;
    }

    public function moveUp() {
        $this->update([
            'posi' => $this->posi - 1,
        ]);
        return $this;
    }

    public function moveDown() {
        $this->update([
            'posi' => $this->posi + 1,
        ]);
        return $this;
    }

    public function prevStep(): ?self
    {
        return $this->howtothread->prevStep($this->posi);
    }

    public function nextStep(): ?self
    {
        return $this->howtothread->nextStep($this->posi);
    }

    public function getRelativeSteps()
    {
        $next = $this->nextStep();
        $prev = $this->prevStep();

        return [
            'current' => $this,
            'prev' => $prev,
            'next' => $next,
        ];
    }

    #endregion
}
