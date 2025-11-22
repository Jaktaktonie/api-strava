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
- Seeder dorzuca też trzech dodatkowych użytkowników z kilkoma przykładowymi aktywnościami, żeby feed/testy miały realne dane.

## Endpointy dostępne obecnie (`/api`)
- `POST /auth/register` – tworzy nowe konto.
- `POST /auth/login` – zwraca token Sanctum dla istniejącego użytkownika.
- `POST /auth/forgot-password` – wysyła link resetujący.
- `POST /auth/reset-password` – zmienia hasło na podstawie tokenu.
- `POST /auth/logout` – unieważnia bieżący token (wymaga Bearer tokenu).
- `GET /profile` – zwraca profil zalogowanego użytkownika.
- `PUT /profile` – aktualizuje dane profilu (personalne + parametry treningowe).
- `GET /me` – skrócony endpoint zwracający `UserResource` dla zalogowanego.
- `GET /activities` – lista własnych aktywności (filtrowanie po typie, dacie, sortowanie po starcie).
- `POST /activities` – zapisanie nowej aktywności z metrykami i śladem GPS.
- `GET /activities/{id}` – szczegóły aktywności (dane liczbowe, notatka, zdjęcia, ślad).
- `PUT /activities/{id}` – edycja aktywności (tytuł, parametry, notatki).
- `DELETE /activities/{id}` – usunięcie aktywności użytkownika.

Pełna specyfikacja znajduje się w Swaggerze pod `/api/documentation` (UI korzysta z hosta ustawionego w `L5_SWAGGER_CONST_HOST`).

## Testy
`php artisan test` – pokrywa rejestrację/logowanie/reset hasła, profil oraz cały CRUD aktywności. Wszystkie testy przechodzą na czystej bazie po seederze.

## Roadmapa / następne kroki
- Eksport aktywności do `.gpx` + przechowywanie plików/zdjęć w storage/S3.
- Endpointy “admin only” (lista aktywności/users, filtrowanie, usuwanie, statystyki globalne) używane przez panel www.
- Moduł społecznościowy: znajomi, zaproszenia, blokady, kudosy, komentarze, feed plus powiadomienia push.
- Ranking/zbiorcze statystyki użytkownika (tydzień/miesiąc) na podstawie nowego modułu aktywności.
- Lokalizacje UI (`locale`, `timezone`) już są – w przyszłych etapach można dodać powiązane komunikaty/maile.
