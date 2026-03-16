<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-i18n="kontakt_title">HYPER | Kontakt</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="scroll-track-container"><div class="scroll-pod-indicator" id="scrollPod"></div></div>
    <div id="nav-placeholder"></div>

    <header class="sub-hero">
        <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2000" class="hero-bg-img" alt="Contact">
        <div class="video-overlay"></div>
        <div class="hero-content">
            <h1 data-i18n="kontakt_h1">Kontakt</h1>
        </div>
    </header>

    <section class="section">
        <div class="grid-2 reveal">
            
            <div style="display: flex; flex-direction: column;">
                <h2 class="section-title" data-i18n="kontakt_napisz_do_nas">Napisz do nas</h2>
                <div class="title-bar"></div>
                <form class="card" method="POST" action="kontakt.php" style="height: auto; flex-grow: 1;">
                    <label style="display: block; margin-bottom: 5px;" data-i18n="kontakt_label_name">Imię i Nazwisko</label>
                    <input type="text" data-i18n="form_name" name="name" placeholder="Jan Kowalski" required>
                    
                    <label style="display: block; margin-bottom: 5px;" data-i18n="kontakt_label_email">Adres E-mail</label>
                    <input type="email" data-i18n="form_email" name="email" placeholder="email@przyklad.pl" required>
                    
                    <label style="display: block; margin-bottom: 5px;" data-i18n="kontakt_label_message">Wiadomość</label>
                    <textarea rows="6" data-i18n="form_msg" name="message" placeholder="Treść wiadomości..." required></textarea>
                    
                    <button type="submit" class="cta-btn" data-i18n="form_btn" style="width: 100%; margin-top: auto;">Wyślij Wiadomość</button>
                </form>
            </div>

            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // 1. Pobieranie danych z formularza
                    $name = strip_tags(trim($_POST["name"]));
                    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
                    $message = trim($_POST["message"]);

                    // 2. Konfiguracja odbiorcy
                    $to = "kn.hypewr@pwr.edu.pl"; // TU WPISZ SWÓJ ADRES
                    $subject = "Nowa wiadomość od: $name";
                    
                    // 3. Budowanie treści maila
                    $email_content = "Imię: $name\n";
                    $email_content .= "Email: $email\n\n";
                    $email_content .= "Wiadomość:\n$message\n";

                    // 4. Nagłówki
                    $headers = "From: strona@hypewr.pwr.edu.pl\r\n";
                    $headers .= "Reply-To: $email\r\n";
                    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                    // 5. Wysyłka
                    if (mail($to, $subject, $email_content, $headers)) {
                        echo "<p style='color: #4CAF50; font-weight: bold; margin-bottom: 20px;'>Dziękujemy! Wiadomość została wysłana.</p>";
                    } else {
                        echo "<p style='color: #F44336; font-weight: bold; margin-bottom: 20px;'>Ups! Coś poszło nie tak. Spróbuj ponownie później (funkcja mail może być zablokowana na serwerze).</p>";
                    }
                }
            ?>

            <div style="display: flex; flex-direction: column;">
                <h2 class="section-title" style="visibility: hidden;" data-i18n="kontakt_dane">Dane</h2> <div class="title-bar" style="visibility: hidden;"></div>
                
                <div class="card" style="display: flex; flex-direction: column; justify-content: center;">
                    <h3 style="margin-bottom: 30px; font-family: var(--font-head);" data-i18n="kontakt_dane_teleadresowe">Dane Teleadresowe</h3>
                    
                    <div style="margin-bottom: 25px;">
                        <span style="color: var(--text-muted); font-size: 0.9rem;" data-i18n="kontakt_adres_email">ADRES E-MAIL</span>
                        <p style="font-size: 1.2rem; color: var(--accent-primary);">kn.hypewr@pwr.edu.pl</p>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <span style="color: var(--text-muted); font-size: 0.9rem;" data-i18n="kontakt_lokalizacja">LOKALIZACJA</span>
                        <p style="font-size: 1.1rem;" data-i18n="kontakt_lokalizacja_text">
                            Politechnika Wrocławska<br>
                            Budynek C-13, pok. 2.14<br>
                            Wybrzeże Wyspiańskiego 27<br>
                            50-370 Wrocław
                        </p>
                    </div>

                    <div style="margin-top: 20px;">
                        <span style="color: var(--text-muted); font-size: 0.9rem; display: block; margin-bottom: 10px;" data-i18n="kontakt_social">SOCIAL MEDIA</span>
                        <div class="social-links">
                            <a href="https://www.facebook.com/kn.hyper" data-i18n="kontakt_social_facebook">Facebook</a>
                            <a href="https://www.linkedin.com/hyper" data-i18n="kontakt_social_linkedin">LinkedIn</a>
                            <a href="https://www.instagram.com//kn_hyper" data-i18n="kontakt_social_instagram">Instagram</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <div id="footer-placeholder"></div>
    <script src="../js/script.js"></script>
</body>
</html>