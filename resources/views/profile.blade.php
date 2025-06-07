<x-layout title="Home Page">
    <h2>Profile</h2>
    @auth
        <p>{{ auth()->user()->name }}!</p>
    @endauth

</x-layout>