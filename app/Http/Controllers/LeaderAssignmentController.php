<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\CampUser;
use App\Models\Help;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Leitende zuteilen (admin/users/leaders): Matrix Teilnehmende × Leitende, jedes Kreuz wird sofort gespeichert.
 * Einzeln geht es weiterhin über "Person bearbeiten" (AdminUsersController).
 */
class LeaderAssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $camp = Auth::user()->camp;
        $participants = CampUser::with(['user', 'leaders'])
            ->where('camp_id', $camp->id)
            ->where('role_id', Role::Teilnehmer)
            ->get()
            ->filter(fn (CampUser $campUser) => $campUser->user)
            ->sortBy(fn (CampUser $campUser) => $campUser->user->username, SORT_NATURAL | SORT_FLAG_CASE)
            ->map(fn (CampUser $campUser) => [
                'id' => $campUser->id,
                'name' => $campUser->user->username,
                'edit_url' => route('admin.users.edit', $campUser->user->slug),
                'leader_ids' => $campUser->leaders->modelKeys(),
            ])
            ->values();
        $leaders = collect($camp->leaderOptions())
            ->map(fn (string $name, int $id) => ['id' => $id, 'name' => $name])
            ->values();

        $title = 'Leitende zuteilen';
        $help = Help::where('title', $title)->first();
        $help['main_title'] = 'Personen';
        $help['main_route'] = '/admin/users';

        return view('admin.users.leaders', compact('participants', 'leaders', 'title', 'help'));
    }

    public function update(CampUser $campUser, User $leader): JsonResponse
    {
        return $this->assign($campUser, $leader, true);
    }

    public function destroy(CampUser $campUser, User $leader): JsonResponse
    {
        return $this->assign($campUser, $leader, false);
    }

    /** Antwortet mit dem gespeicherten Stand; im Demo-Kurs bleibt alles, wie es ist. */
    private function assign(CampUser $campUser, User $leader, bool $assigned): JsonResponse
    {
        $camp = Auth::user()->camp;
        // Nur Personen und Leitende des eigenen Kurses, sonst liessen sich fremde Kurse verändern.
        abort_unless($campUser->camp_id === $camp->id, 403);
        abort_unless(array_key_exists($leader->id, $camp->leaderOptions()), 422, 'Diese Person ist im Kurs nicht leitend.');

        if (! Auth::user()->demo) {
            if ($assigned) {
                $campUser->leaders()->syncWithoutDetaching([$leader->id]);
            } else {
                $campUser->leaders()->detach($leader->id);
            }
        }

        return response()->json(['assigned' => $campUser->leaders()->whereKey($leader->id)->exists()]);
    }
}
