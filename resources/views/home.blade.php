<x-layout title="Home Page">
    <h2>Welcome to the Home Page</h2>
    @auth
        <p>Welcome back, {{ auth()->user()->name }}!</p>
        <a href="{{ route('item.create') }}" class="btn btn-primary">Click Me</a>
    @endauth

</x-layout>