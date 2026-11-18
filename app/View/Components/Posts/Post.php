<?php

namespace App\View\Components\Posts;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Post extends Component
{
    public $posts;

    public $showLeader;

    public $title;

    public $editable;

    public $user;

    /** Als Karte wie «Bemerkung» (Quali, Vergleich); ohne Rückmeldungen dann gar nicht angezeigt. */
    public bool $card;

    /** Lange Texte auf drei Zeilen gekürzt, mit «Mehr» zum Aufklappen (Profil). */
    public bool $clamp;

    /** Karte füllt die Höhe des Elternelements, die Liste scrollt darin (Profil am Computer). */
    public bool $fill;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($posts, $showLeader, $title, $editable = true, $user = null, bool $card = false, bool $clamp = false, bool $fill = false)
    {
        //
        $this->posts = $posts;
        $this->showLeader = $showLeader;
        $this->title = $title;
        $this->editable = $editable;
        $this->user = $user;
        $this->card = $card;
        $this->clamp = $clamp;
        $this->fill = $fill;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.posts.post');
    }
}
