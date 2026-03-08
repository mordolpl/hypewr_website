# Rozszerzenie strony HYPEwR o system bloga (PHP + PostgreSQL)

W tej gałęzi dodano prosty system wpisów (aktualności) dostępny w zakładce **Projekty**. Możliwe jest tworzenie, edytowanie i usuwanie postów poprzez panel administratora.

## Struktura plików

- `config.php` – parametry połączenia z bazą danych PostgreSQL
- `db/schema.sql` – definicje tabel (`users`, `posts`) i przykładowy wpis admina
- `includes/` – pomocnicze moduły: `db.php`, `functions.php` (operacje na wpisach)
- `pages/projekty.php` – dynamiczna strona projektów z wyświetlaniem postów
- `post.php` – osobna strona pojedynczego wpisu
- `admin/` – panel administracyjny
  - `login.php`, `dashboard.php`, `post_form.php`, `logout.php`

Pozostałe pliki HTML/CSS/JS zostały zachowane, a linki do `projekty.html` zastąpiono `projekty.php`.

## Instalacja i konfiguracja

1. **Serwer PHP**
   - Umieść pliki na serwerze obsługującym PHP 7.4+.
   - Upewnij się, że moduł PDO z obsługą PostgreSQL jest zainstalowany (`pdo_pgsql`).

2. **Baza danych PostgreSQL**
   - Stwórz bazę danych, np. `hyper_blog`.
   - Uruchom skrypt schematu:
     ```sh
     psql -U postgres -d hyper_blog -f db/schema.sql
     ```
   - Utwórz konto administratora by wysłać hasło:
     ```php
     <?php
echo password_hash('TwojeHaslo', PASSWORD_DEFAULT);
     ?>
     ```
     następnie wstawić wynik do tabeli:
     ```sql
     INSERT INTO users (username, password_hash) VALUES ('admin', '...');
     ```

3. **Ustawienia połączenia**
   - Edytuj `config.php` i podaj prawidłowe dane (`DB_USER`, `DB_PASS`, itd.).

4. **Dostęp do panelu admina**
   - Przejdź do `/admin/login.php` i zaloguj się używając konta z kroku wyżej.
   - Po zalogowaniu zobaczysz listę wpisów i przycisk do dodawania nowych.

5. **Dodawanie wpisów**
   - Po utworzeniu wpisy wyświetlą się automatycznie na stronie `pages/projekty.php`.
   - Kliknięcie tytułu przenosi do pojedynczej strony `post.php?id=<id>`.

## Uwagi dodatkowe

* Styl panelu admina wykorzystuje istniejący `css/style.css` z drobnymi ograniczeniami CSS osadzonymi wewnątrz plików.
* Zabezpieczenia są minimalne – w środowisku produkcyjnym warto dodać filtrowanie, CSRF, silniejsze uwierzytelnianie oraz HTTPS.
* Jeżeli w przyszłości będziesz chciał generować dynamicznie inne strony, możesz zmienić rozszerzenia `.html` na `.php` i wykorzystać wspólne nagłówki/stopki.

---

**Aktualizacja plików front-endowych**

- `js/script.js`: linki nawigacyjne i odkrywanie aktywnej zakładki zostały dopasowane do rozszerzeń `.php`.
- `index.html`: przycisk CTA prowadzi teraz do `pages/projekty.php`.

W razie dodatkowych pytań lub potrzeby rozbudowy systemu daj znać!