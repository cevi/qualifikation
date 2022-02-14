<?php

namespace App\Models;

use Parental\HasParent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HitobitoUser extends User {
    use HasParent;

    public function __construct(...$args) {
        $this->fillable[] = 'hitobito_id';
        parent::__construct(...$args);
        $this->email_verified_at = Carbon::now();
    }
}
