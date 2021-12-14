<?php

namespace Tests\Controllers;

use App\Models\Drink;
use App\Http\Controllers\DrinkController as DrinkController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DrinkControllerTest extends TestCase {
    use RefreshDatabase;
    public function testDrinkIndexReturnsArray() {
        $this->json('get', '/api/drink/index')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'drinks' => []
                ]
            );

        // I only test for an array here because the drink list
        // could be empty, based on the functionality I've provided
        // to the user (e.g the ability to delete drinks).
    }

    public function testDrinkEditReturnsASingleDrink() {

        /* This is to test that the API returns data for a specific drink to 
        *  the frontend for a specific drink such that it can be displayed
        *  in the edit form. Yep, there's a better way to do this other than requesting a
        *  specific drink ID.  For the sake of brevity and for the scope of
        *  this project, I'm just gonna request a drink ID I know exists, though
        *  in a production environment this particular ID could easily go poof,
        *  breaking the test.
        */

        // REFACTOR TO FIND A REAL DRINK ID FROM DB

        $response = $this->json('get', '/api/drink/edit/1')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [
                    'drink' => []
                ]
            );

        $this->assertEquals(1, count($response['drink']));
    }


    public function testDrinkCommit() {

        /* This tests the actual addition of a drink to the DB.
        *  The term 'commit' is used to describe the action associated with
        *  actually adding something to the DB, as opposed to displaying a form
        *  on the frontend to add something. Hence, we use a 201 response
        *  instead of 200 for the sake of the test. The controller returns
        *  integer 1 to indicate a successful insertion.
        */

        /* ADDENDUM: This only checks that the controller says "yes!" when
        *  asked to insert something.  A more proper and thorough test would be
         * to A) insert the thing, B) check the thing actually exists in the DB, and then
         * C) delete the thing to keep things clean. Even better... don't do a test
         * like this on a production DB in the first place.
         */

        $response = $this->json(
            'POST',
            '/api/drink/commit/' .
            'ZOMG Caffeine Drink/' .
            '300/' .
            '1/')
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    'drink' =>
                        [
                            'id',
                            'name',
                            'mg_caffeine_per_serving',
                            'servings_per_container'
                        ],
                    'created',
                ]);
        return $response;
    }

    public function testDrinkUpdateCommit() {
        /* This tests the action to modify a drink in the DB.  There are a number of ways to do this.  Mainly,
        *  it depends on how robust the testing suite needs to be (with stubs, mocking, faker, etc.).  Here,
         * however, this is a basic situation where you simply need to make sure the data was updated properly
         * for a known record.  Since we're using RefreshDatabase, which rolls back any changes after each test,
         * calling testDrinkCommit() from here won't help.  I prefer to keep each test granular even if it means
         * duplicating a data insert.  Obviously this could mean both the add and the update tests will require
         * modification if the Drink table structure changes, but those should be relatively easy fixes, and I'd
         * already be editing this file in that case anyway.  Given the scope of THIS project as defined, it's
         * not going to create a mountain of trouble to do it this way.  Larger-scale projects are another matter entirely.
         * All this to say:  I simply create a test record, retrieve it, and test that the update was executed properly.
         */

        $drink = Drink::create([
                'name' => '100 lb Gorilla',
                'mg_caffeine_per_serving' => '100',
                'servings_per_container' => '2']
        );

        $drink->save();

        /* Retrieve the drink we just inserted. This may be unreliable on high-volume production databases,
         * but here, it'll work.
         */
        $drink = Drink::find($drink->id);

        /* I like to re-retrieve the DB record because I don't trust the $drink until it actually contains
         * the actual record.  It's all unicorns and fairy dust until it's actually written to the platter.
         * Paranoia built on years of experience I guess. (And yes, I know hard drives aren't platter-based anymore).
         * The concat-slash notation here is because the actual Vue side didn't like me passing in objects to post requests.
         * Instead of chasing that goose for the sake of time, I modified the controllers and routes to simply accept a string of arguments.
         * Ideally, I'd rather pass in an object.
         */

        $response = $this->json(
            'POST',
            '/api/drink/update/' .
            $drink->id . '/' .
            '200 lb Female Gorilla/' .
            '200/' .
            '3')
            ->assertStatus(Response::HTTP_CREATED);


        // Now that we've written our change to the DB, time to retrieve it again and run our assertions.
        $drink = Drink::find($drink->id);

        $this->assertSame('200 lb Female Gorilla', $drink->name);
        $this->assertSame(200, $drink->mg_caffeine_per_serving);
        $this->assertSame(3.0, $drink->servings_per_container);

        return $response;

    }

    public function testDrinkDelete() {

        $drink = Drink::create([
                'name' => '300 lb Gorilla',
                'mg_caffeine_per_serving' => '100',
                'servings_per_container' => '2']
        );

        $drink->save();

        $drink = Drink::find($drink->id);

        $response = $this->json(
            'POST',
            '/api/drink/delete/' . $drink->id)
            ->assertStatus(Response::HTTP_CREATED);

        // Make sure the drink was actually deleted
        $this->assertNull(Drink::find($drink->id));
    }

    public function testDrinkLimitList() {
        // Test that the API call for the caffeine drink list returns an array and OK status
        // Once again, we need a drink to test on. The controller expects a drink ID and the quantity consumed.
        $drink = Drink::create([
                'name' => 'Mr. Fizz',
                'mg_caffeine_per_serving' => '75',
                'servings_per_container' => '1']
        );

        $drink->save();
        $drinksConsumed = 2; // I don't like magic numbers but this one is only used here and it's not real data.
        // This value is normally passed in by DrinkController::drinkLimitList()
        $response = $this->json(
            'POST',
            '/api/drink/getDrinkLimitList/' . $drink->id . '/' . $drinksConsumed)
            ->assertStatus(Response::HTTP_OK);
        $this->assertIsArray($response['drinks']);

    }
    public function testCaffeineLimitByDrink() {
        // Test that the function that calculates each drink's limit is correct

        /* Part of me wanted to do calculation testing on each drink, since there aren't many.  But in the
        * spirit of the API, we have to assume this will become an enterprise application with a ton of users,
         * in which case running a test (even a simple one) on each drink could take some serious time.  Instead,
         * I pass in some dummy values.
         */

        /* The functionality to calculate an individual drink's remaining servings isn't exposed to the API consumer through a route,
           so we call the drink model directly. With more time, I would write more (less static) tests for this.
        */
        //$controller = new DrinkController();
        $drink = Drink::create([
                'name' => 'Walter White Blue Cola',
                'mg_caffeine_per_serving' => '30',
                'servings_per_container' => '1']
        );

        $mgRemaining = 125; // This is normally calculated by DrinkController::drinkLimitList(), but we just use a dummy value here.
        $drink->calculateServingsRemaining($mgRemaining);
        $this->assertEquals(4, $drink->servingsRemaining);

    }

}


