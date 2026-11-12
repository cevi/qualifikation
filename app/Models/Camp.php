<?php

namespace App\Models;

use App\Enums\Role;
use App\Enums\SurveyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Camp extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        // here assign this team to a global user with global default role
        self::created(function (Camp $camp) {
            // get the admin user and assign roles/permissions on new team model
            CampUser::create([
                'user_id' => 1,
                'role_id' => Role::Administrator,
                'camp_id' => $camp['id'],
            ]);
        });
    }

    protected $fillable = [
        'name', 'year', 'user_id', 'camp_type_id', 'group_id', 'foreign_id', 'secondsurveyopen', 'global_camp', 'finish', 'counter', 'status_control', 'survey_status_id', 'end_date', 'checklist_hidden',
    ];

    protected $casts = [
        'secondsurveyopen' => 'boolean',
        'global_camp' => 'boolean',
        'demo' => 'boolean',
        'finish' => 'boolean',
        'status_control' => 'boolean',
        'checklist_hidden' => 'boolean',
        // Nur mit Status-Steuerung gesetzt (null = keine Steuerung).
        'survey_status_id' => SurveyStatus::class,
    ];

    public function users()
    {
        return $this->hasMany('App\Models\User');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function allUsers()
    {
        return $this->belongsToMany('App\Models\User', 'camp_users')->where('camp_users.role_id', '<>', Role::Administrator);
    }

    public function participants()
    {
        return $this->allUsers()->where('camp_users.role_id', Role::Teilnehmer)->OrderBy('username');
    }

    /** Teilnehmende, bei denen die angemeldete Person zu den Leitenden gehört. */
    public function my_participants()
    {
        return $this->participants()->whereExists($this->ledByCurrentUser(...));
    }

    public function other_participants()
    {
        return $this->participants()->whereNotExists($this->ledByCurrentUser(...));
    }

    private function ledByCurrentUser(QueryBuilder $query): void
    {
        $query->select(DB::raw(1))->from('camp_user_leader')
            ->whereColumn('camp_user_leader.camp_user_id', 'camp_users.id')
            ->where('camp_user_leader.leader_id', Auth::id());
    }

    /** Mögliche Leitende im Kurs (Gruppen-, Kurs- und Stabsleitende) als [user_id => Name], nach Name sortiert. */
    public function leaderOptions(): array
    {
        $userIds = CampUser::where('camp_id', $this->id)
            ->whereIn('role_id', [Role::Gruppenleiter, Role::Kursleiter, Role::Stabsleiter])
            ->pluck('user_id');

        return User::whereIn('id', $userIds)->orderBy('username')->pluck('username', 'id')->all();
    }

    public function camp_users()
    {
        return $this->hasMany(CampUser::class)->where('camp_users.role_id', Role::Teilnehmer);
    }

    public function camp_users_all()
    {
        return $this->hasMany(CampUser::class)->where('camp_users.role_id', '<>', Role::Administrator);
    }

    public function camp_type()
    {
        return $this->belongsTo('App\Models\CampType');
    }

    public function chaptersWithQuestions()
    {
        $campType = $this->camp_type;

        if (! $campType) {
            return collect();
        }

        $chapters = Chapter::with('questions');

        if ($campType->default_type) {
            $chapters->where('default_chapter', true);
        } else {
            $chapters->where('camp_type_id', $campType->id);
        }

        return $chapters->get();
    }

    public function group()
    {
        return $this->belongsTo('App\Models\Group');
    }

    public function survey_status()
    {
        return $this->belongsTo(SurveyStatus::class);
    }

    public function surveys()
    {
        return $this->hasManyThrough(Survey::class, CampUser::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function standardtexts()
    {
        return $this->hasMany('App\Models\Standardtext');
    }
}
