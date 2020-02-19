@extends('layouts.app')

@section('content')

<div class="mx-auto h-full flex justify-center items-center bg-gray-300">
    <div class="w-96 bg-blue-900 rounded-lg shadow-xl p-6">
        <svg class="fill-current text-white w-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M3.9 4.2c-.5 0-.8.3-.8.8s.4.8.8.8c.5 0 .8-.4.8-.8.1-.5-.3-.8-.8-.8zM3.3 18.6c0 1.4-.2 2.1-1.5 2.1-.3 0-.7 0-.9-.1l-.3 1.1c.3.1.7.2 1.1.2 1.9 0 2.7-1.2 2.7-3.2V8.1H3.3v10.5zM9.4 17.5c1.4 0 2.3-.4 3-1.2.8-1 1.1-2.1 1.1-3.8 0-1.4-.2-2.7-1-3.5-.6-.7-1.5-1.1-2.9-1.1s-2.3.4-3 1.2c-.8 1-1.1 2.2-1.1 3.8 0 1.5.2 2.6 1 3.5.6.7 1.5 1.1 2.9 1.1zM7.5 9.7c.3-.4.9-.8 2-.8 1 0 1.6.3 1.9.7.5.6.7 1.7.7 2.9s-.2 2.4-.7 3.1c-.3.4-.9.8-2 .8-1 0-1.6-.3-1.9-.7-.5-.6-.7-1.6-.7-2.9 0-1.2.2-2.4.7-3.1zM15 14.4c0 2.1.4 3.1 2.5 3.1.6 0 1.3-.1 1.8-.2l-.1-1c-.5.1-1 .2-1.5.2-1.4 0-1.5-.6-1.5-2.1v-5h3V8.3h-3v-3l-1.2.2v2.7h-1.8v1.1H15v5.1zM6 18h17v1H6z"/>
        </svg>
        <h1 class="text-white text-3xl pt-8">Join Us</h1>
        <h2 class="text-blue-300">Enter your information below</h2>
        <form method="POST" action="{{ route('register') }}" class="pt-8">
                        @csrf

                        <div class="relative">
                            <label for="name" class="uppercase text-blue-500 text-xs font-bold absolute pl-3 pt-2">Full Name</label>

                            <div class="">
                                <input id="name" type="name" class="pt-8 w-full rounded p-3 bg-blue-800 text-gray-100 outline-none focus:bg-blue-700" placeholder="Name" name="name" value="{{ old('name') }}"  autocomplete="name" autofocus>

                                @error('name')
                                    <span class="text-red-500 pt-1 text-sm" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="relative pt-3">
                            <label for="email" class="uppercase text-blue-500 text-xs font-bold absolute pl-3 pt-2">E-mail</label>

                            <div class="">
                                <input id="email" type="email" class="pt-8 w-full rounded p-3 bg-blue-800 text-gray-100 outline-none focus:bg-blue-700" placeholder="your@email.com" name="email" value="{{ old('name') }}"  autofocus>

                                @error('email')
                                    <span class="text-red-500 pt-1 text-sm" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="relative pt-3">
                            <label for="password" class="uppercase text-blue-500 text-xs font-bold absolute pl-3 pt-2">Password</label>

                            <div class="">
                                <input id="password" type="password" class="pt-8 w-full rounded p-3 bg-blue-800 text-gray-100 outline-none focus:bg-blue-700" placeholder="Password" name="password"  required autocomplete="new-password">

                                @error('password')
                                    <span class="text-red-500 pt-1 text-sm" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="relative pt-3">
                            <label for="password-confirm" class="uppercase text-blue-500 text-xs font-bold absolute pl-3 pt-2">Re-Enter Password</label>

                            <div class="">
                                <input id="password-confirm" type="password" class="pt-8 w-full rounded p-3 bg-blue-800 text-gray-100 outline-none focus:bg-blue-700" placeholder="Confirm" name="password_confirmation"  required autocomplete="new-password">

                            </div>
                        </div>





                        <div class="pt-8">
                            <button type="submit" class="w-full bg-gray-400 py-2 px-3 text-left uppercase rounded text-blue-800 font-bold">
                                Register
                            </button>
                        </div>

                        <div class="pt-8 flex justify-between text-white text-sm font-bold">
                            <a class="" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                            <a class="" href="{{ route('login') }}">
                                Login
                            </a>

                        </div>
                    </form>

    </div>
</div>
@endsection
