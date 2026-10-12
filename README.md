# Reading List App

Reading List App je mala Laravel web aplikacija za pretraživanje i spremanje knjiga. Korisnik može pretraživati knjige putem Open Library API-ja, pregledati rezultate i spremiti odabrane knjige u vlastitu bazu.

Spremljene knjige mogu se pregledavati i filtrirati na javnoj stranici, dok Filament admin panel omogućuje potpuno upravljanje knjigama, uključujući kreiranje, uređivanje, brisanje, statuse, ocjene i kategorije.

Projekt koristi Laravel, Livewire, Filament, Tailwind CSS i Alpine.js.

# Popis vanjskih paketa i API-ja

## PHP i Laravel paketi

Paketi se instaliraju i upravljaju pomoću Composera:

- **Laravel Framework** (`laravel/framework`) – osnovni PHP framework za routing, HTTP klijenta, Eloquent ORM, migracije, validaciju i rad s bazom podataka.
- **Livewire** (`livewire/livewire`) – izrada interaktivnih komponenti bez potrebe za pisanjem zasebnog JavaScript API sloja.
- **Livewire Blaze** (`livewire/blaze`) – podrška za optimizaciju Livewire komponenti.
- **Laravel Tinker** (`laravel/tinker`) – interaktivni rad s Laravel aplikacijom kroz komandnu liniju.
- **Filament** (`filament/filament`) – administracijsko sučelje za upravljanje knjigama, statusima, ocijenama i kategorijama.
- **Laravel Activitylog** (`spatie/laravel-activitylog`) – bilježenje promjena nad modelima, uključujući kreiranje i izmjenu knjiga.
- **Laravel Flash** (`laracasts/flash`) – podrška za flash poruke u aplikaciji.
- **PHP** – aplikacija zahtjeva PHP `8.3` ili noviji u okviru definiranog Composer ograničenja.

## Frontend paketi

Frontend paketi se instaliraju pomoću npm-a:

- **Tailwind CSS** (`tailwindcss`) – utility-first CSS framework za stiliziranje i responzivni dizajn.
- **Tailwind CSS Vite plugin** (`@tailwindcss/vite`) – integracija Tailwind CSS-a sa Vite build procesom.
- **Laravel Vite plugin** (`laravel-vite-plugin`) – povezivanje Laravel aplikacije sa Vite-om.
- **Vite** (`vite`) – razvojni server i alat za build frontend resursa.
- **Vite Plus** (`vite-plus`) – dodatna Vite konfiguracija i alatni sloj projekta.
- **Alpine.js** – koristi se kroz Livewire/Flux frontend sloj za lokalnu interaktivnost, uključujući otvaranje i zatvaranje modala.

## Razvojni i testni paketi

Ovi paketi se koriste tokom razvoja, testiranja, provjere tipova i formatiranja koda:

- **Pest** (`pestphp/pest`) – testni framework.
- **Pest Laravel plugin** (`pestphp/pest-plugin-laravel`) – Laravel integracija za Pest.
- **PHPStan / Larastan** (`larastan/larastan`) – statička analiza PHP i Laravel koda.
- **Laravel Pint** (`laravel/pint`) – formatiranje i provjera stila PHP koda.
- **Mockery** (`mockery/mockery`) – kreiranje mock objekata za testove.
- **Faker** (`fakerphp/faker`) – generiranje testnih podataka.
- **Laravel Sail** (`laravel/sail`) – opcionalno lokalno Docker okruženje za Laravel.
- **Laravel Pail** (`laravel/pail`) – praćenje logova aplikacije iz komandne linije.
- **Laravel Boost** (`laravel/boost`) – razvojni alati za Laravel aplikaciju.

## Korišteni vanjski API

### Open Library API

Za pretraživanje knjiga koristi se javni **Open Library Search API**:

- Osnovni endpoint: `https://openlibrary.org/search.json`
- HTTP metoda: `GET`
- Parametar pretrage: `title`
- Maksimalan broj rezultata: `6`
- Polja koja se preuzimaju: `key`, `title`, `author_name`, `cover_i`, `isbn`, `editions` i `editions_description`

API poziv je izdvojen u servis:

```text
app/Services/OpenLibraryService.php
```

Naslovne slike knjiga učitavaju se preko Open Library Covers API-ja:

```text
https://covers.openlibrary.org/b/id/{cover_id}-M.jpg
```

Za korištenje Open Library API-ja nije potreban API ključ. Ako API nije dostupan ili vrati neuspješan HTTP odgovor, aplikacija prikazuje prazne rezultate pretrage.

# Pokretanje projekta

## 1. Preduvjeti

- PHP kompatibilan s projektom
- Composer
- Node.js i npm
- SQLite ili druga konfigurirana baza podataka

## 2. Kloniranje repozitorija

```bash
git clone https://github.com/msaravanj/ReadingListApp.git
cd ReadingListApp
```

## 3. Instalacija paketa

```bash
composer install
```

## 4. Konfiguracija okruženja

Kreirati `.env` iz predloška:

```bash
cp .env.example .env
php artisan key:generate
```

U `.env` datoteci konfigurirati vezu s bazom podataka.

## 5. Migracije i početni podaci

```bash
php artisan migrate --seed
```

## 6. Pokretanje aplikacije

U terminalu pokrenuti aplikaciju:

```bash
composer run dev
```

Aplikacija je dostupna na http://localhost:8000.

Filament administratorsko sučelje dostupno je na http://localhost:8000/admin.

Administratorskog korisnika kreirati pomoću:

```bash
php artisan make:filament-user
```

## Kako Filament gradi sučelje

Filament koristi **server-driven UI** pristup, što znači da se sučelje definira na backendu pomoću PHP koda, a Filament na temelju tih definicija generira prikaz u pregledniku.

**Resource** predstavlja administracijski dio za određeni model, primjerice `Book`, te povezuje podatke s obrascima i tablicama.

- **Form schema** definira polja obrasca, njihova svojstva i validaciju.
- **Table schema** definira stupce tablice, sortiranje, filtriranje i dostupne akcije.

Na taj način Filament automatski generira sučelje za pregled, dodavanje i uređivanje podataka, uz manje ponavljajućeg koda i jednostavnije održavanje aplikacije.

## Kako funkcioniraju paketi u Laravelu

Laravel koristi Composer za instalaciju i upravljanje PHP paketima trećih strana. Paketi omogućuju dodavanje funkcionalnosti bez potrebe za pisanjem svega od početka.

- **`composer`** – alat za instalaciju, ažuriranje i upravljanje PHP ovisnostima.
- **`composer.json`** – definira potrebne pakete i ograničenja njihovih verzija.
- **`composer.lock`** – bilježi točne verzije instaliranih paketa kako bi svi koristili iste verzije prilikom instalacije pomoću `composer install`.

### Instalacija i registracija paketa

Paket se instalira naredbom:

```bash
composer require naziv/paketa
```

Neki Laravel paketi koriste **Service Provider**, koji registrira njihove servise, konfiguraciju i druge mogućnosti unutar aplikacije. Laravel može automatski pronaći i registrirati provider putem mehanizma **Package Auto-Discovery**, pa ručna registracija često nije potrebna.

Ako paket ne podržava automatsku registraciju, njegov Service Provider može se ručno dodati u konfiguraciju aplikacije, ovisno o verziji Laravela i uputama paketa.

### Filament pluginovi

Filament pluginovi proširuju administratorsko sučelje dodatnim komponentama i funkcionalnostima. Nakon instalacije plugin se, ovisno o njegovoj implementaciji, registrira u odgovarajućem Panel Provideru, najčešće u metodi `panel()`.

Primjer:

```php
use Vendor\Package\ExamplePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            ExamplePlugin::make(),
        ]);
}
```

Naziv klase i način registracije ovise o konkretnom pluginu, pa je potrebno slijediti njegovu dokumentaciju. Sama instalacija Composer paketa ne znači nužno da je Filament plugin aktiviran u panelu.
