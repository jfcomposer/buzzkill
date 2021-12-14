<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Drink extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'mg_caffeine_per_serving',
        'servings_per_container',
        'servingsRemaining'
    ];

    public function calculateServingsRemaining($mgRemaining) {
        // This functionality was originally in the DrinkController.  I decided to move it here in the spirit of
        // fat models, thin controllers.  It's also one less argument to be passed around since a drink object
        // already knows its mg per serving.  I'm still divided on whether this logic should live here and am
        // completely open to refactoring it back into the controller.

        if($mgRemaining < 1) {
            // This check is here because we need to display all the drinks, even if each has zero servings left
            $this->servingsRemaining = 0;
            return;
        }

        if($this->mg_caffeine_per_serving == 0) {
            $this->servingsRemaining = -1; // I was torn on what to return here. -1 will be the stand-in for "infinite"
        }

        $this->servingsRemaining = (int) ($mgRemaining / $this->mg_caffeine_per_serving);
        //return $this->servingsRemaining;
    }
}
