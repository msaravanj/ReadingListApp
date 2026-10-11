# Reading List App

Reading List App je mala Laravel web aplikacija za pretraživanje i spremanje knjiga. Korisnik može pretraživati knjige putem Open Library API-ja, pregledati rezultate i spremiti odabrane knjige u vlastitu bazu.

Spremljene knjige mogu se pregledavati i filtrirati na javnoj stranici, dok Filament admin panel omogućuje potpuno upravljanje knjigama, uključujući kreiranje, uređivanje, brisanje, statuse, ocjene i kategorije.

Projekt koristi Laravel, Livewire, Filament, Tailwind CSS i Alpine.js.

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
