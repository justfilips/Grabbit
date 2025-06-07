<x-layout title="Home Page">
    <h2>Welcome to the Home Page</h2>
    @auth
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    @endauth

</x-layout>