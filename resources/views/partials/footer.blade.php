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

        <div class="footer-nav">
            <div class="footer-nav-section">
                <h1>Plugins</h1>
                <div class="footer-nav-section-items">
                    <a href="https://ultrapermissions.com" target="_blank" class="footer-nav-section-items__item">Ultra Permissions</a>
                    <a href="https://ultrascoreboards.com" target="_blank" class="footer-nav-section-items__item">Ultra Scoreboards</a>
                    <a href="https://ultrapunishments.com" target="_blank" class="footer-nav-section-items__item">Ultra Punishments</a>
                    <a href="https://ultracustomizer.com" target="_blank" class="footer-nav-section-items__item">Ultra Customizer</a>
                    <a href="https://ultraeconomy.com" target="_blank" class="footer-nav-section-items__item">Ultra Economy</a>
                    <a href="https://ultraregions.com" target="_blank" class="footer-nav-section-items__item">Ultra Regions</a>
                    <a href="https://ultramotd.com" target="_blank" class="footer-nav-section-items__item">Ultra MOTD</a>
                    <a href="https://insaneshops.com" target="_blank" class="footer-nav-section-items__item">Insane Shops</a>
                    <a href="https://insanespawners.com" target="_blank" class="footer-nav-section-items__item">Insane Vaults</a>
                    <a href="https://insaneannouncer.com" target="_blank" class="footer-nav-section-items__item">Insane Announcer</a>
                </div>
            </div>
            <div class="footer-nav-section">
                <h1>Marketplaces</h1>
                <div class="footer-nav-section-items">
                    <a href="https://www.spigotmc.org/resources/authors/techscode.29620" class="footer-nav-section-items__item">SpigotMC</a>
                    <a href="https://polymart.org/user/techscode.5485" class="footer-nav-section-items__item">Polymart</a>
                    <a href="https://craftaro.com" class="footer-nav-section-items__item">Craftaro</a>
                </div>
            </div>
        </div>

    </div>
    <div class="app-footer__line">
        <span>Hello World!!</span>
    </div>
    <div class="app-footer__lower">
        <div class="app-footer__lower-copy-right">
            © 2021 - {{ @date('Y') }} Techscode. All rights reserved.
        </div>
    </div>

</footer>
