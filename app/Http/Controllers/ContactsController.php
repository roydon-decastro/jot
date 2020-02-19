<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Http\Resources\Contact as ContactResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactsController extends Controller
{
    //
    public function index() {
        // get the contacts of the user of the request
        // need to build relationship between user and their contacts
        $this->authorize('viewAny', Contact::class);
        // OLD
        // return request()->user()->contacts;
        // NEW
        return ContactResource::collection(request()->user()->contacts);

    }

    public function store() {

        //OLD
        // Contact::create($this->validateData());
        // NEW get user from request, then use user-contacts relationship to create/store a contact
        // this will create a user_id automatically
        $this->authorize('create', Contact::class);
        // OLD
        // request()->user()->contacts()->create($this->validateData());
        // NEW
        // when we save, laravel automatically gives back the saved record so we can work on it immediately, you just need to save it into a new variable
        $contact = request()->user()->contacts()->create($this->validateData());
        // Response Symphony HTTP_CREATED == 201
        return (new ContactResource($contact))->response()->setStatusCode(Response::HTTP_CREATED);

    }

    public function show(Contact $contact) 
    {
        // OLD
        // if(request()->user()->isNot($contact->user)) {
        //     return response([], 403);
        // }
        // NEW - using ContactsPolicy. Is the user authorized to view the contact?
        $this->authorize('view', $contact);
        // OLD
        // return $contact;
        // NEW
        return new ContactResource($contact);

    }

    public function update(Contact $contact)
    {
        // OLD
        // if(request()->user()->isNot($contact->user)) {
        //     return response([], 403);
        // }
        // NEW - using ContactsPolicy. Is the user authorized to update the contact?
        $this->authorize('update', $contact);
        $contact->update($this->validateData());
        // Response Symphony HTTP_OK == 200
        return (new ContactResource($contact))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(Contact $contact)
    {
        // OLD
        // if(request()->user()->isNot($contact->user)) {
        //     return response([], 403);
        // }
        // NEW - using ContactsPolicy. Is the user authorized to delete the contact?
        $this->authorize('delete', $contact);
        $contact->delete();
        return response([], Response::HTTP_NO_CONTENT);
    }

    private function validateData() {
        return request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'birthday' => 'required',
            'company' => 'required'
        ]);
    }
}
