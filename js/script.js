document.addEventListener('DOMContentLoaded', () => {
    
    // --- 0. MODERN PAGE TRANSITION ---
    // Handle browser back button cache (bfcache)
    window.addEventListener('pageshow', (event) => {
        if (event.persisted || document.body.classList.contains('page-exit')) {
            document.body.classList.remove('page-exit');
        }
    });

    // Intercept internal link clicks
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;
        
        const targetUrl = link.getAttribute('href');
        
        if (
            !targetUrl || 
            targetUrl.startsWith('#') || // anchors
            targetUrl.startsWith('mailto:') || // emails
            targetUrl.startsWith('tel:') || // phones
            link.target === '_blank' || // new tabs
            (targetUrl.startsWith('http') && !targetUrl.includes(window.location.hostname)) 
        ) {
            return;
        }

        e.preventDefault();
        document.body.classList.add('page-exit');

        setTimeout(() => {
            window.location.href = targetUrl;
        }, 200); // 0.2s duration matches CSS transition
    });
    
    // --- 1. SŁOWNIK TŁUMACZEŃ ---
    const translations = {
        "pl": {
            // Nawigacja
            "nav_home": "Dom",
            "nav_projects": "Projekty",
            "nav_team": "Zespół",
            "nav_sponsors": "Sponsorzy",
            "nav_recruitment": "Rekrutacja",
            "nav_contact": "Kontakt",
            
            // strony
            "projekty_title": "HYPEwR | Projekty",
            "projekty_h1": "Nasze Projekty",

            // Hero (Strona główna)
            "hero_title": "Przyszłość<br>Transportu",
            "hero_subtitle": "Tworzymy kolej magnetyczną nowej generacji. Szybciej. Ekologiczniej. Bliżej.",
            "hero_cta": "Zobacz Projekty",

            // Misja
            "mission_title": "Nasza Misja",
            "mission_head": "Rewolucja zaczyna się we Wrocławiu",
            "mission_text_1": "Nie budujemy tylko pojazdu. Tworzymy nowy standard podróżowania. Naszym celem jest opracowanie technologii <strong>Hyperloop</strong> – piątego środka transportu.",
            "mission_text_2": "Jako zespół inżynierów z Politechniki Wrocławskiej, skupiamy się na trzech filarach:",
            "mission_list_1": "➤ <strong>Innowacja:</strong> Autorski silnik liniowy i lewitacja.",
            "mission_list_2": "➤ <strong>Precyzja:</strong> Kompozytowe poszycie.",
            "mission_list_3": "➤ <strong>Skalowalność:</strong> Rozwiązania dla przemysłu.",
            
            // Karta EHW
            "card_ehw_title": "European Hyperloop Week",
            "card_ehw_desc": "Naszym głównym celem jest dominacja podczas międzynarodowych zawodów EHW w Zurychu.",
            "card_ehw_edition": "EDYCJA 2026",
            "card_ehw_loc": "Veendam, NL",

            // Timeline & News
            "timeline_title": "Roadmap & Aktualności",
            "timeline_head_left": "Oś Czasu Projektu",
            "event_1_date": "Styczeń 2025",
            "event_1_title": "Testy Silnika LIM",
            "event_1_desc": "Pomyślne zakończenie testów statycznych napędu liniowego.",
            "event_2_date": "Marzec 2025",
            "event_2_title": "Premiera Poszycia V1",
            "event_2_desc": "Prezentacja nowej, aerodynamicznej kapsuły z włókna węglowego.",
            "event_3_date": "Lipiec 2025",
            "event_3_title": "European Hyperloop Week",
            "event_3_desc": "Wyjazd zespołu do Zurychu na finały zawodów.",
            "news_head_right": "Wyróżnione Wydarzenie",
            "news_badge": "NEWS",
            "news_title": "Nowe Laboratorium",
            "news_desc": "Otworzyliśmy nowe laboratorium dedykowane testom w warunkach próżniowych.",
            "news_link": "Czytaj więcej ->",

            // HUD (Statystyki)
            "hud_speed": "Prędkość Projektowa",
            "hud_team": "Członków Zespołu",
            "hud_co2": "Emisji CO2",
            "hud_year": "Rok Wdrożenia V1",

            // Footer
            "footer_univ": "Politechnika Wrocławska",
            "footer_rights": "&copy; 2026 Koło Naukowe HYPER.",

            // Formularz Kontaktowy (Placeholdery)
            "form_name": "Imię i Nazwisko",
            "form_email": "Adres E-mail",
            "form_msg": "Treść wiadomości...",
            "form_btn": "Wyślij Wiadomość"
        },
        "en": {
            // Navigation
            "nav_home": "Home",
            "nav_projects": "Projects",
            "nav_team": "Team",
            "nav_sponsors": "Sponsors",
            "nav_recruitment": "Recruitment",
            "nav_contact": "Contact",

            // pages
            "projekty_title": "HYPEwR | Projects",
            "projekty_h1": "Our Projects",

            // Hero
            "hero_title": "Future of<br>Transport",
            "hero_subtitle": "Creating next-gen magnetic railway. Faster. Greener. Closer.",
            "hero_cta": "View Projects",

            // Mission
            "mission_title": "Our Mission",
            "mission_head": "Revolution starts in Wrocław",
            "mission_text_1": "We are not just building a vehicle. We are creating a new travel standard. Our goal is to develop <strong>Hyperloop</strong> technology – the fifth mode of transport.",
            "mission_text_2": "As a team of engineers from Wrocław University of Science and Technology, we focus on three pillars:",
            "mission_list_1": "➤ <strong>Innovation:</strong> Proprietary linear motor and levitation.",
            "mission_list_2": "➤ <strong>Precision:</strong> Carbon fiber shell.",
            "mission_list_3": "➤ <strong>Scalability:</strong> Industry-ready solutions.",

            // Card EHW
            "card_ehw_title": "European Hyperloop Week",
            "card_ehw_desc": "Our main goal this year is domination during the international EHW competition in Zurich.",
            "card_ehw_edition": "EDITION 2026",
            "card_ehw_loc": "Veendam, NL",

            // Timeline & News
            "timeline_title": "Roadmap & News",
            "timeline_head_left": "Project Timeline",
            "event_1_date": "January 2025",
            "event_1_title": "LIM Engine Tests",
            "event_1_desc": "Successful completion of static tests of the new linear drive.",
            "event_2_date": "March 2025",
            "event_2_title": "Shell V1 Premiere",
            "event_2_desc": "Presentation of the new aerodynamic carbon fiber pod.",
            "event_3_date": "July 2025",
            "event_3_title": "European Hyperloop Week",
            "event_3_desc": "Team departure to Zurich for the competition finals.",
            "news_head_right": "Featured Event",
            "news_badge": "NEWS",
            "news_title": "New Vacuum Lab",
            "news_desc": "We opened a new laboratory dedicated to tests in low-pressure conditions.",
            "news_link": "Read more ->",

            // HUD
            "hud_speed": "Design Speed",
            "hud_team": "Team Members",
            "hud_co2": "CO2 Emission",
            "hud_year": "V1 Launch Year",

            // Footer
            "footer_univ": "Wrocław University of Science and Technology",
            "footer_rights": "&copy; 2026 HYPER Scientific Circle.",

            // Contact Form
            "form_name": "Full Name",
            "form_email": "E-mail Address",
            "form_msg": "Your Message...",
            "form_btn": "Send Message"
        }
    };

    // --- 2. GENEROWANIE NAWIGACJI (Z obsługą i18n) ---
    const navPlaceholder = document.getElementById('nav-placeholder');
    if (navPlaceholder) {
        const currentPath = window.location.pathname.split('/').pop() || 'index.html';

        navPlaceholder.innerHTML = `
        <nav>
            <div class="logo">HYPE<span>R</span></div>
            
            <div class="hamburger" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>

            <div class="nav-container" id="navMenu">
                <div class="nav-links">
                    <a href="${currentPath === 'index.html' ? 'index.html' : '../index.html'}" class="${currentPath === 'index.html' ? 'active' : ''}" data-i18n="nav_home">Home</a>
                    <a href="${currentPath === 'index.html' ? 'pages/projekty.php' : 'projekty.php'}" class="${currentPath === 'projekty.php' || currentPath === 'projekty.html' || currentPath === 'post.php' ? 'active' : ''}" data-i18n="nav_projects">Projekty</a>
                    <a href="${currentPath === 'index.html' ? 'pages/zespol.html' : 'zespol.html'}" class="${currentPath === 'zespol.html' ? 'active' : ''}" data-i18n="nav_team">Zespół</a>
                    <a href="${currentPath === 'index.html' ? 'pages/sponsorzy.html' : 'sponsorzy.html'}" class="${currentPath === 'sponsorzy.html' ? 'active' : ''}" data-i18n="nav_sponsors">Sponsorzy</a>
                    <a href="${currentPath === 'index.html' ? 'pages/rekrutacja.html' : 'rekrutacja.html'}" class="${currentPath === 'rekrutacja.html' ? 'active' : ''}" data-i18n="nav_recruitment">Rekrutacja</a>
                    <a href="${currentPath === 'index.html' ? 'pages/kontakt.html' : 'kontakt.html'}" class="${currentPath === 'kontakt.html' ? 'active' : ''}" data-i18n="nav_contact">Kontakt</a>
                </div>

                <div class="lang-switch">
                    <span class="lang-btn" id="btn-pl" onclick="changeLang('pl')">PL</span>
                    <span class="lang-btn" id="btn-en" onclick="changeLang('en')">EN</span>
                </div>
            </div>
        </nav>
        `;
    }

    // --- 3. GENEROWANIE STOPKI (Z obsługą i18n) ---
    const footerPlaceholder = document.getElementById('footer-placeholder');
    if (footerPlaceholder) {
        // Determine base path for images based on current page location
        const isInPages = window.location.pathname.includes('/pages/') || window.location.href.includes('pages');
        const imageBasePath = isInPages ? '../media/images/' : 'media/images/';
        
        footerPlaceholder.innerHTML = `
        <footer>
            <div class="footer-left">
                <div class="footer-logos">
                    <a href="https://hyper.pwr.edu.pl/" style="text-decoration: none;">
                        <img src="${imageBasePath}logo.png" alt="Logo HYPER" style="margin-right: 20px;">
                    </a>
                    <a href="https://pwr.edu.pl/" style="text-decoration: none;">
                        <img src="${imageBasePath}pwr.png" alt="Logo PWr">
                    </a>
                </div>
                <div class="footer-info">
                    <p><strong>HYPER</strong><br><span data-i18n="footer_univ">Politechnika Wrocławska</span></p>
                </div>
            </div>
            <div class="footer-right">
                <p>kn.hyper@pwr.edu.pl</p>
                <p style="opacity: 0.5; margin-top: 5px;" data-i18n="footer_rights">&copy; ${new Date().getFullYear()} Koło Naukowe HYPER.</p>
            </div>
        </footer>
        `;
    }

    // --- 4. LOGIKA JĘZYKOWA ---
    // Pobierz język z localStorage lub domyślnie 'pl'
    let currentLang = localStorage.getItem('language') || 'pl';
    
    // Funkcja globalna (dostępna dla onclick w HTML)
    window.changeLang = function(lang) {
        currentLang = lang;
        localStorage.setItem('language', lang); // Zapisz wybór
        updateContent();
    };

    function updateContent() {
        // 1. Zaktualizuj wszystkie elementy z atrybutem data-i18n
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (translations[currentLang][key]) {
                // Jeśli to input/textarea, zmieniamy placeholder, w przeciwnym razie innerHTML
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                    element.placeholder = translations[currentLang][key];
                } else {
                    element.innerHTML = translations[currentLang][key];
                }
            }
        });

        // 2. Zaktualizuj stan przycisków PL/EN
        document.getElementById('btn-pl').classList.toggle('active', currentLang === 'pl');
        document.getElementById('btn-en').classList.toggle('active', currentLang === 'en');
    }

    // Inicjalizacja języka na start
    updateContent();


    // --- 5. POZOSTAŁE SKRYPTY (Scroll, Reveal, Hamburger) ---
    // (Kod scrollbara i animacji pozostaje bez zmian, wklejam skrótowo)
    
    // Hamburger Logic
    window.toggleMenu = function() {
        const nav = document.getElementById('navMenu');
        const hamburger = document.querySelector('.hamburger');
        nav.classList.toggle('active');
        hamburger.classList.toggle('active');
    }

    // Scrollbar Logic
    const track = document.querySelector('.scroll-track-container');
    const pod = document.getElementById('scrollPod');
    let isDragging = false;
    let startY, startTopPercent;

    if (track && pod) {
        const updatePodPosition = () => {
            if (isDragging) return;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPercent = window.scrollY / docHeight;
            const validPercent = Math.max(0, Math.min(scrollPercent, 1));
            pod.style.top = (validPercent * 94) + '%';
        };
        window.addEventListener('scroll', updatePodPosition);

        pod.addEventListener('mousedown', (e) => {
            isDragging = true; startY = e.clientY;
            const rect = track.getBoundingClientRect();
            const podRect = pod.getBoundingClientRect();
            startTopPercent = (podRect.top - rect.top) / rect.height;
            document.body.style.userSelect = 'none'; pod.style.cursor = 'grabbing';
            pod.style.background = '#ffffff';
        });
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return; e.preventDefault();
            const trackRect = track.getBoundingClientRect();
            const deltaY = e.clientY - startY;
            const deltaPercent = deltaY / trackRect.height;
            let newPercent = startTopPercent + deltaPercent;
            newPercent = Math.max(0, Math.min(newPercent, 0.94));
            pod.style.top = (newPercent * 100) + '%';
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            window.scrollTo(0, (newPercent / 0.94) * docHeight);
        });
        document.addEventListener('mouseup', () => {
            isDragging = false; document.body.style.userSelect = '';
            pod.style.cursor = 'grab'; pod.style.background = '';
        });
        track.addEventListener('click', (e) => {
            if (e.target === pod) return;
            const trackRect = track.getBoundingClientRect();
            const clickY = e.clientY - trackRect.top;
            let percent = clickY / trackRect.height - (pod.offsetHeight / 2 / trackRect.height);
            percent = Math.max(0, Math.min(percent, 0.94));
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            window.scrollTo({ top: (percent / 0.94) * docHeight, behavior: 'smooth' });
        });
    }

    // Reveal Animation
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('active');
        });
    }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});