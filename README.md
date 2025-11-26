# MiniStrava API

Backend serwujący dane dla aplikacji mobilnej Flutter oraz panelu administracyjnego. Udostępnia rejestrację/logowanie, zarządzanie profilem i dokumentację OpenAPI, stanowiąc bazę do dalszej rozbudowy (aktywności, feed, ranking, eksport GPX).

## Stos technologiczny
- PHP 8.2+, Laravel 12, Sanctum (tokeny) i Breeze API (auth).
- MySQL/MariaDB jako baza danych.
- L5 Swagger do generowania dokumentacji (`/api/documentation`).
- Docker/Sail opcjonalnie – repo pozwala też uruchomić klasyczny stack lokalny (PHP + MySQL).

## Uruchomienie lokalne
1. Skopiuj konfigurację: `cp .env.example .env` i wypełnij parametry bazy/maili.
2. Zainstaluj zależności: `composer install` (opcjonalnie `npm install`, jeśli używasz frontu Breeze).
3. Wygeneruj klucz aplikacji: `php artisan key:generate`.
4. Uruchom migracje z seedem kont startowych: `php artisan migrate --seed` (lub `php artisan migrate:fresh --seed` przy czyszczeniu).
5. Start serwera: `php artisan serve` (domyślnie http://localhost:8000). Sail/Docker: `./vendor/bin/sail up` i `sail artisan migrate --seed`.
6. (Opcjonalnie) Wygeneruj dokumentację: `php artisan l5-swagger:generate`.

## Konta testowe (można nadpisać w `.env`)
- Admin: `admin@ministrava.dev / Admin123!` – rola `admin`, do logowania w panelu.
- Użytkownik: `tester@ministrava.dev / User123!` – zwykłe konto do testów mobilki.

## Endpointy dostępne obecnie (`/api`)
- `POST /auth/register` – tworzy nowe konto.
- `POST /auth/login` – zwraca token Sanctum dla istniejącego użytkownika.
- `POST /auth/forgot-password` – wysyła link resetujący.
- `POST /auth/reset-password` – zmienia hasło na podstawie tokenu.
- `POST /auth/logout` – unieważnia bieżący token (wymaga Bearer tokenu).
- `GET /profile` – zwraca profil zalogowanego użytkownika.
- `PUT /profile` – aktualizuje dane profilu (personalne + parametry treningowe).
- `GET /me` – skrócony endpoint zwracający `UserResource` dla zalogowanego.

Pełna specyfikacja znajduje się w Swaggerze pod `/api/documentation` (UI korzysta z hosta ustawionego w `L5_SWAGGER_CONST_HOST`).

## Testy
`php artisan test` – weryfikuje scenariusze rejestracji, logowania, resetu hasła oraz endpointy Breeze/Profilu. Wszystkie obecne testy przechodzą na czystej bazie po seederze.

## Roadmapa / następne kroki
- Definicja aktywności (tabele, metryki, ślad GPS) wraz z CRUD/API i eksportem `.gpx`.
- Seed z co najmniej pięcioma użytkownikami i przykładowymi aktywnościami wymaganymi w specyfikacji.
- Endpointy “admin only” (lista aktywności/users, filtrowanie, usuwanie, statystyki globalne) używane przez panel www.
- Moduł społecznościowy: znajomi, zaproszenia, blokady, kudosy, komentarze, feed plus powiadomienia push.
- Lokalizacje UI (`locale`, `timezone`) już są – w przyszłych etapach można dodać powiązane komunikaty/maile.
