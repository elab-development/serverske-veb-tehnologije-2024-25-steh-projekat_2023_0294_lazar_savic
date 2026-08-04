# Veb aplikacija za nekretnine - STEH domaci/projekat

## 1. O Projektu

Ovaj projekat predstavlja veb platformu namenjenu za oglašavnje nekretnina koje se prodaju ili rentiraju. Sistem pruža sveobuhvatnu infrastrukturu za pretragu, ponudu i analitiku tržišta nekretnina, uz podršku za različite nivoe pristupa korisnika (administratori, agenti i korisnici).

---

## 2. Arhitektura i Tehnologije

Projekat je razvijen poštujući MVC arhitekturni šablon i moderne principe razvoja REST servisa.

- **Framework:** Laravel 11.x
- **Jezik:** PHP 8.2+
- **Baza podataka:** MySQL / MariaDB
- **Autentifikacija:** Laravel Sanctum (Token-based API authentication)
- **Eksterni servisi:**
    - Frankfurter Exchange Rates API (Konverzija valuta)
    - Open-Meteo Weather Forecast API (Vremenska prognoza)

### Ključne tehničke karakteristike:

- Kompletan CRUD nad resursima uz naprednu validaciju (Form Requests)
- MySQL Trigeri i DB Transakcije za očuvanje integriteta podataka
- Zaštita resursa po ulogama (Role Middleware + Laravel Policy)
- Izvorno keširanje čestih upita radi optimizacije performansi (`Illuminate\Support\Facades\Cache`)
- Podrška za obradu i skladištenje fajlova (Upload slika)
- Dinamički eksport analitičkih izveštaja u CSV formatu

---

## 3. Uslovi za Pokretanje

Pre instalacije i pokretanja projekta, neophodno je imati instalirano sledeće okruženje:

- PHP >= 8.2 sa sledećim ekstenzijama: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer (PHP Package Manager)
- MySQL Server >= 8.0 ili MariaDB >= 10.4
- Git

---

## 4. Podešavanje i Instalacija

Pratite sledeće korake za lokalno podizanje projekta:

### 1. Kloniranje repozitorijuma

```bash
git clone <https://github.com/elab-development/serverske-veb-tehnologije-2024-25-steh-projekat_2023_0294_lazar_savic>
cd steh-nekretnine
```

### 2. Instalacija zavisnosti

```bash
composer install
```

### 3. Podešavanje okruženja

Kopirajte primer fajla okruženja i generišite ključ aplikacije:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfiguracija baze podataka

U `.env` fajlu podesite parametre za konekciju sa vašim MySQL serverom:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=steh_nekretnine
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Pokretanje migracija i seeder-a

Izvršite migracije baze zajedno sa kreiranjem trigera i početnih podataka:

```bash
php artisan migrate --seed
```

### 6. Kreiranje symlink-a za storage (upload fajlova)

```bash
php artisan storage:link
```

### 7. Pokretanje lokalnog razvojnog servera

```bash
php artisan serve
```

API će biti dostupan na adresi `http://127.0.0.1:8000`.

---

## 5. Korisničke Uloge i Privilegije

Sistem podržava kontrolu pristupa baziranu na ulogama (RBAC):

| Uloga        | Opis i Prava Pristupa                                                                                                      |
| :----------- | :------------------------------------------------------------------------------------------------------------------------- |
| **Gost**     | Pregled nekretnina i gradova, pretraga i filtriranje, kalkulator kredita, eksterni API servisi, zahtev za reset lozinke.   |
| **Korisnik** | Sve privilegije Gosta + slanje upita za nekretnine, pregled sopstvenih kalkulacija i izmena lozinke.                       |
| **Agent**    | Sve privilegije Korisnika + kreiranje i izmena sopstvenih nekretnina, pregled pristiglih upita i promena njihovog statusa. |
| **Admin**    | Pun pristup sistemu: upravljanje svim nekretninama, dodavanje i brisanje gradova, brisanje upita i upravljanje nalozima.   |

---

## 6. Pregled API Endpoint-a

Svi API zahtevi moraju imati zaglavlja:

- `Accept: application/json`
- `Authorization: Bearer <TOKEN>` (za zaštićene rute)

### Autentifikacija i Lozinka

- `POST /api/register` - Registracija novog korisnika
- `POST /api/login` - Prijava na sistem (vraća Sanctum token)
- `POST /api/logout` - Odjava sa sistema
- `POST /api/forgot-password` - Slanje tokena za reset lozinke
- `POST /api/reset-password` - Izmena lozinke uz token

### Nekretnine

- `GET /api/nekretnine` - Lista svih nekretnina (paginirano)
- `GET /api/nekretnine/pretraga` - Napredna pretraga i filtriranje
- `GET /api/nekretnine/{id}` - Detalji određene nekretnine
- `POST /api/nekretnine` - Kreiranje nekretnine sa slikom (Agent/Admin)
- `PUT /api/nekretnine/{id}` - Izmena nekretnine (Vlasnik/Admin)
- `DELETE /api/nekretnine/{id}` - Brisanje nekretnine (Admin)
- `GET /api/nekretnine/{id}/upiti` - Pregled svih upita za nekretninu (Ugnježđena ruta)

### Gradovi i Analitika

- `GET /api/gradovi` - Lista gradova (Keširano)
- `GET /api/gradovi/statistika` - Pregled broja nekretnina po gradu
- `GET /api/gradovi/{id}/nekretnine` - Nekretnine u određenom gradu (Ugnježđena ruta)
- `GET /api/gradovi/analitika-trzista` - Složeni SQL izveštaj sa agregacijom
- `GET /api/gradovi/export-csv` - Eksport analitičkog izveštaja u CSV formatu
- `POST /api/gradovi` - Dodavanje novog grada (Admin)
- `DELETE /api/gradovi/{id}` - Brisanje grada (Admin)

### Upiti i Kalkulacije

- `GET /api/upiti` - Lista pristiglih upita (Agent/Admin)
- `POST /api/upiti` - Slanje novog upita za nekretninu
- `PATCH /api/upiti/{id}/status` - Izmena statusa upita (Agent/Admin)
- `GET /api/kalkulacije` - Istorija izračunatih kredita
- `POST /api/kalkulacije/izracunaj` - Procena mesečne rate stambenog kredita

### Eksterni API Servisi

- `GET /api/nekretnine/{id}/konvertuj-cenu` - Preračun cene u USD, CHF, GBP (Frankfurter API)
- `GET /api/vremenska-prognoza` - Trenutna vremenska prognoza za lokaciju (Open-Meteo API)

---

## 7. Bezbednost i Dobre Prakse

- **SQL Injection:** Onemogućen primenom Laravel Eloquent ORM-a i parametrizovanih PDO upita.
- **IDOR Zaštita:** Implementirana preko `NekretninaPolicy` klase — korisnici ne mogu menjati niti brisati tuđe resurse.
- **Enkripcija:** Sve lozinke se skladište isključivo kao `Bcrypt` mešavine.
- **Validacija:** Svi ulazni podaci se pročišćavaju kroz Form Request klase pre obrade.

---

## 8. Testiranje API-ja

Za testiranje servisa preporučuje se korišćenje alata Postman ili Insomnia. U bazi se nakon izvršavanja seeder-a nalaze podrazumevani test nalozi sa lozinkom `password123`:

- `admin@steh.rs` (Uloga: admin)
- `agent@steh.rs` (Uloga: agent)
- `user@steh.rs` (Uloga: korisnik)
