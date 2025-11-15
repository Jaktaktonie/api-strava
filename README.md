# MiniStrava API

Backend REST API projektu MiniStrava odpowiedzialny za:
- obsługę kont użytkowników (rejestracja, logowanie, reset hasła, profile),
- zapis i analizę aktywności wraz z danymi GPS, zdjęciami, komentarzami i polubieniami,
- zarządzanie relacjami społecznymi (znajomi, blokady, zgłoszenia nadużyć),
- dostarczenie statystyk użytkownika oraz rankingów,
- panel administracyjny (autoryzacja, statystyki globalne, moderacja treści),
- dokumentację OpenAPI/Swagger i eksporty GPX.

Technologie docelowe:
- Laravel + PHP 8.2,
- MySQL (hosting hostido),
- Redis (cache/kolejki),
- Swagger (darkaonline/l5-swagger) dla `/api/documentation`.

Repo zawiera tylko warstwę API; aplikacja mobilna i panel webowy będą pracować na tych endpointach. Dalsze instrukcje uruchomienia zostaną dodane po skonfigurowaniu środowiska.
