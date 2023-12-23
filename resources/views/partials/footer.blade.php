<footer class="app-footer">

    <div class="app-footer__upper">

        <div class="footer-about">
            <div class="footer-about-logo">
                <img src="{{ \Illuminate\Support\Facades\Vite::asset('resources/images/logo.png') }}" alt="Logo">
                TechsCode
            </div>
            <div class="footer-about-text">
                Forging the ultimate customer relationship and Minecraft plugin experiences.
            </div>
            <div class="footer-about-socials">
                <a href="https://twitter.com/TechsCode" target="_blank" class="footer-about-socials__item">
                    <x-icon-twitter-outline />
                </a>
                <a href="https://discord.techscode.com" target="_blank" class="footer-about-socials__item">
                    <x-icon-discord-outline />
                </a>
                <a href="https://www.youtube.com/@TechsCode" target="_blank" class="footer-about-socials__item">
                    <x-icon-youtube-outline />
                </a>
                <a href="https://www.linkedin.com/company/techscode" target="_blank" class="footer-about-socials__item">
                    <x-icon-linkedin-outline />
                </a>
                <a href="https://www.instagram.com/techscodes/" target="_blank" class="footer-about-socials__item">
                    <x-icon-instagram-outline />
                </a>
            </div>
        </div>

    </div>
    <div class="app-footer__line">
        <span>Hello World!!</span>
    </div>
    <div class="app-footer__lower">
        <div class="app-footer__lower__copy-right">
            © 2021 - {{ @date('Y') }} Techscode. All rights reserved.
        </div>
    </div>

</footer>
