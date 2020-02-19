<?php

namespace Tests\Feature;
use App\Contact;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        // this will run before any test run
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /** @test */
    public function a_list_of_contacts_can_be_fetched_for_the_authenticated_user() {

        $this->withoutExceptionHandling();

        // create 2 users using factory
        $user = factory(User::class)->create();
        $anotherUser = factory(User::class)->create();

        // create 2 contacts connecting them to the users above using userid
        $contact = factory(Contact::class)->create(['user_id' => $user->id]);
        $anotherContact = factory(Contact::class)->create(['user_id' => $anotherUser->id]);

        $response = $this->get('/api/contacts?api_token=' . $user->api_token);

        $response->assertJsonCount(1);
        //check if we're getting the proper id of contact for user
        // OLD
        // $response->assertJson([['id'=> $contact->id]]);
        // NEW
        $response->assertJson([
            "data" => [
                [
                "data" => [
                    'contact_id' => $contact->id
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function unauthenticated_user_should_be_redirected_to_login() {

        // try to post to database new contact from data
        // $response = $this->post('/api/contacts/', $this->data());
        // overwrite api_token to blank
        $response = $this->post( '/api/contacts', array_merge( $this->data(), ['api_token' => '']));


        // check if they are redirected to login page
        $response->assertRedirect('/login');

        // check if no record has been added, we expect 0
        $this->assertCount(0, Contact::all());
    }


    public function a_contact_can_be_added() {

        $this->withoutExceptionHandling();

        $this->post( '/api/contacts', $this->data());
        $contact = Contact::first();

        // $this->assertCount( 1, $contact);
        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('05/14/1988', $contact->birthday);
        $this->assertEquals('ABC String', $contact->company);
    }

    /** @test */
    public function an_authenticated_user_can_add_a_contact() {

        // $this->withoutExceptionHandling();
        //use faker to create a new user
        // $user = factory(User::class)->create();

        // dd($user->api_token);
        // OLD
        // $this->post( '/api/contacts', array_merge( $this->data(), ['api_token' => $user->api_token]));
        // OLD - 2
        // $this->post( '/api/contacts', $this->data());
        // NEW
        $response = $this->post( '/api/contacts', $this->data());
        $contact = Contact::first();

        // dd(json_decode($response->getContent()));

        // $this->assertCount( 1, $contact);
        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('05/14/1988', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('ABC String', $contact->company);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'contact_id' => $contact->id,
            ],
            'links' => [
                'self' => url('/contacts/' . $contact->id),
            ]
        ]);
    }    

    /** @test */
    public function fields_are_required() {
        collect(['name', 'email', 'birthday', 'company'])
        ->each(function ($field){
            $response = $this->post( '/api/contacts', array_merge($this->data(), [$field => '']));

            $response->assertSessionHasErrors($field);
            $this->assertCount(0, Contact::all());

        });
    }

    /** @test */
    public function email_must_be_a_valid_email()
    {
        $response = $this->post( '/api/contacts', array_merge($this->data(), ['email' => 'NOT AN EMAIL']));

        $response->assertSessionHasErrors('email');
        $this->assertCount(0, Contact::all());

    }

    /** @test */
    public function birthdays_are_properly_stored()
    {
        $response = $this->post( '/api/contacts', array_merge($this->data(), ['birthday' => 'May 14, 1988']));

        $this->assertCount(1, Contact::all());
        $this->assertInstanceOf(Carbon::class, Contact::first()->birthday);
        $this->assertEquals('05-14-1988',  Contact::first()->birthday->format('m-d-Y'));
    }

    /** @test */
    public function a_contact_can_be_retrieved()
    {

        //create contact on database --OLD
        // $contact = factory(Contact::class)->create();
        // NEW - pass user->id when creating a new contact
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]); 

        
        //attempt to fetch record on db
        $response = $this->get('/api/contacts/' . $contact->id . '?api_token=' . $this->user->api_token);
        
        // dd(json_decode($response->getContent()));
        //check if we have data on the response
        $response->assertJson([
            'data' => [
                'contact_id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'birthday' => $contact->birthday->format('m/d/Y'),
                'company' => $contact->company,    
                'last_updated' => $contact->updated_at->diffForHumans(),
            ]
        ]);
    }

    /** @test */
    public function only_the_users_contacts_can_be_retrieved()
    {
        // create a contact using id from the user created above in the setup method
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);
        // create a new user
        $anotherUser = factory(User::class)->create();
        // get the newly created contact using the id of the anotherUser, which should not be allowed
        $response = $this->get('/api/contacts/' . $contact->id . '?api_token=' . $anotherUser->api_token);
        // check if we allowed or not the assertion above
        $response->assertStatus(403);
    }

    /** @test */
    public function a_contact_can_be_patched()
    {
        $this->withoutExceptionHandling();
        // create a new random contact via factory
        // OLD
        // $contact = factory(Contact::class)->create();
        // NEW create a contact using this->user->id attached to it
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

        // modify/update new random contact via factory to data sample below
        $response = $this->patch('/api/contacts/' . $contact->id, $this->data());

        // get fresh copy of the contact after patch
        $contact = $contact->fresh();

        // test the patched record 
        $this->assertEquals('Test Name', $contact->name);
        $this->assertEquals('test@email.com', $contact->email);
        $this->assertEquals('05/14/1988', $contact->birthday->format('m/d/Y'));
        $this->assertEquals('ABC String', $contact->company);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'data' => [
                'contact_id' => $contact->id,
            ],
            'links' => [
                'self' => $contact->path(),
            ]
        ]);

    }

    /** @test */
    public function only_the_owner_of_the_contact_can_patch_the_contact(){

        // create a new random contact via factory
        $contact = factory(Contact::class)->create();

        //create a new user
        $anotherUser = factory(User::class)->create();

        // submit a patch request, but use anotherUser api_token
        $response = $this->patch('/api/contacts/' . $contact->id, 
            array_merge($this->data(), ['api_token' => $anotherUser->api_token]));
        // since we are using anotherUser->api_token, this should give a status of 403, let's check
        $response->assertStatus(403);

    }


    /** @test */
    public function a_contact_can_be_deleted()
    {
        // create a new random contact via factory
        // OLD
        // $contact = factory(Contact::class)->create();
        // NEW
        $contact = factory(Contact::class)->create(['user_id' => $this->user->id]);

        // delete new random contact 
        $response = $this->delete('/api/contacts/' . $contact->id, ['api_token' => $this->user->api_token]);

        // check if record was indeed deleted, expected count of 0
        $this->assertCount(0, Contact::all());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    
    /** @test */
    public function only_the_owner_of_the_contact_can_delete_the_contact(){

        
        // create a new random contact via factory
        $contact = factory(Contact::class)->create();

        //create a new user
        $anotherUser = factory(User::class)->create();
        
        // delete new random contact 
        $response = $this->delete('/api/contacts/' . $contact->id, ['api_token' => $this->user->api_token]);

        // check if record was indeed deleted, expected count of 0
        $response->assertStatus(403);
    }

    private function data() {
        return [
            'name' => 'Test Name',
            'email' => 'test@email.com',
            'birthday' => '05/14/1988',
            'company' => 'ABC String',
            'api_token' => $this->user->api_token,
        ];
    }

}
