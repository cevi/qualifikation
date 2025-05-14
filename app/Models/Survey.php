<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Survey extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'survey_status_id', 'slug', 'camp_user_id', 'comment',
    ];

    protected $casts = [
        'demo' => 'boolean',
    ];

    public function campUser()
    {
        return $this->belongsTo('App\Models\CampUser');
    }

    public function MySurvey()
    {
        $aktUser = Auth::user();
        $camp_user = CampUser::where('user_id', $aktUser['id'])->where('camp_id', $aktUser->camp['id'])->first();

        return $this->camp_user_id === $camp_user['id'] || $this->camp_user->leader_id === $aktUser['id'];
    }

    public function SurveyName()
    {
        $aktUser = Auth::user();
        $name = 'Qualifikation';
        if ($aktUser->isTeilnehmer() && $this['survey_status_id'] < config('status.survey_tnAbgeschlossen')) {
            $name = $this['survey_status_id'] <= config('status.survey_1offen') ? '1. Selbsteinschätzung' : '2. Selbsteinschätzung';
        }

        return $name;
    }

    public function chapters()
    {
        return $this->HasMany('App\Models\SurveyChapter');
    }

    public function questions()
    {
        return $this->hasManyThrough('App\Models\SurveyQuestion', 'App\Models\SurveyChapter');
    }

    public function survey_status()
    {
        return $this->belongsTo('App\Models\SurveyStatus');
    }

    public function TNisAllowed()
    {
        $aktUser = Auth::user();
        $result = $aktUser->isAdmin();
        if(!$result){
            $result = ($aktUser->isLeader() && $this->campUser->leader['id'] == $aktUser['id'] && $this['survey_status_id'] < config('status.survey_fertig'));
            if(!$result) {
                $camp = $aktUser->camp;
                if ($camp['status_control']){
                    $max_status = $camp['survey_status_id'];
                }
                else {
                    $max_status = config('status.survey_tnAbgeschlossen');
                }

                if (($this['survey_status_id'] < $max_status) &&
                    ($aktUser->isTeilnehmer() && $this->campUser->user['id'] == $aktUser['id'])) {
                    $result = ($this['survey_status_id'] <= config('status.survey_1offen'));
                    if (!$result) {
                        $result = $camp['secondsurveyopen'];
                    }
                }
            }
        }
        return $result;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
