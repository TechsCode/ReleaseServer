<header class="app-header">

    <a href="{{ route('home') }}">
        <div class="app-header__logo">
            <img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo.png') }}" alt="Logo">
            {{ config('app.name') }}
        </div>
    </a>

    <div class="nav-items-right">
        <a href="https://techscode.com" target="_blank">
            <div class="nav-items-right__nav-item">
                View all plugins
            </div>
        </a>
        <a href="https://discord.techscode.com" target="_blank">
            <div class="nav-items-right__nav-item">
                Join our Discord
            </div>
        </a>
    </div>

</header>
