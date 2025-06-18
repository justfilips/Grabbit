<x-layout :title="'Register'">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h2 class="text-center mb-4" data-translate>Register</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label" data-translate>Full Name</label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label" data-translate>Email Address</label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label" data-translate>Password</label>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label" data-translate>Confirm Password</label>
                    <input 
                        type="password" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        required
                    >
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100" data-translate>Register</button>

                <p class="text-center mt-3">
                    <span data-translate>Already have an account?</span> 
                    <a href="{{ route('login.form') }}">Login here</a>
                </p>
            </form>
        </div>
    </div>
</x-layout>

<script>
async function translatePage(targetLang) {
    const elements = [...document.querySelectorAll('[data-translate]')];
    const texts = elements.map(el => el.placeholder || el.textContent);

    const response = await fetch('/translate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            texts,
            target: targetLang
        })
    });

    const data = await response.json();

    data.translations.forEach((translated, i) => {
        if(elements[i].placeholder) {
            elements[i].placeholder = translated;
        } else {
            elements[i].textContent = translated;
        }
    });
}
</script>
