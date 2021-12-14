<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Drink;

class DrinkController extends BaseController
{
    use ValidatesRequests;
    protected $caffeineLimit = 500; // The maximum safe amount of caffeine. Defined here to avoid magic numbers elsewhere.
    // I made it a class member because, in context, this doesn't seem like a value that would change often (in which case it should be in the DB)

    public function index() {
        $drinks = Drink::all();
        return response()->json(
                ['drinks' => $drinks
            ]);
    }

    public function addDrink(Request $request) {
        /* This is the action to create the form to add a drink.  Adding the drink to the DB
        is performed in commitDrink().
        */

    }
    public function drinkAddCommit(Request $request, $name, $mg, $servings) {

        $drink = Drink::create([
            'name' => $name,
            'mg_caffeine_per_serving' => $mg,
            'servings_per_container' => $servings]
        );

        try {
            $drink->save();
        } catch(\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'data' => [
                    ['error' => 'Drink creation failed.  Please try again']
                ]
            ], 200);
        }

        return response()->json(
            [
            'drink' => $drink, 'created' => '1'
            ],
         201 );
    }

    public function editDrink(Request $request, $id) {
        try {
            $drink = Drink::where('id', $id)->get();
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'data' => [
                    ['error' => 'Drink not found.  Please try another drink.']
                ]
            ]);
        }

        return response()->json(
            ['drink' => $drink
            ]);
    }

    public function drinkUpdateCommit(Request $request, $id, $name, $mg, $servings) {
        // Method to accept a drink ID and data to update a specific drink record.

        $request = $request->all();

        try {
            $drink = Drink::find($id);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'data' => [
                    ['error' => 'Drink not found.  Please try another drink.']
                ]
            ]);
        }

        $drink->name = $name;
        $drink->mg_caffeine_per_serving = $mg;
        $drink->servings_per_container = $servings;

        try {
            $drink->save() ;
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'data' => [
                        ['error' => 'Failed to update drink. Please try again.']
                    ]
                ]
            );
        }

        return response()->json(
            [
                'drink' => $drink, 'created' => '1'
            ],
            201 );
    }

    public function deleteDrink(Request $request, $id) {

        try {
            $drink = Drink::find($id);
            $drink->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'data' => [
                        ['error' => 'Failed to delete drink. Please try again.']
                    ]
                ]
            );

        }

        return response()->json(
            [
                'status' => 'success',
            ], 201);

    }

    public function drinkLimitList($drinkId, $quantity)
    {
        // Iterates through the drinks in the DB, calling calculateServingsRemaining() on the drink object, and returns an array
        // First, get the drink object based on the ID passed in.

        $requestedDrink = Drink::find($drinkId);

        $mgRemaining = $this->caffeineLimit - ($requestedDrink->mg_caffeine_per_serving * $quantity);
        $drinks = Drink::all();
        $responseDrinks = [];

        foreach($drinks as $drink) {
            // The logic for calculating how many more drinks the user can have was moved from the controller to the model.
            // The thinking was "why don't we ask the DRINK how many more they can have?"  I waffled on this change for a bit.
            // The nice thing is, it cleans up the controller quite a bit and moves drink-based calculations to the Drink itself.

            $drink->calculateServingsRemaining($mgRemaining);
            $responseDrinks[] = $drink;
        }

        return response()->json(
            [
                'drinks' => $responseDrinks
            ], 200);
    }

}
