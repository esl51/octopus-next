# Octopus Next

## Installation

### Backend

```bash
git clone git@github.com:esl51/octopus-next.git ./
composer update
cp .env.example .env
php artisan key:generate --ansi
mkdir -p storage/app/public/media
php artisan storage:link
```
Edit `.env` and set your database connection details
```bash
php artisan migrate:fresh --seed
```

### Frontend

#### Production

```bash
npm install
```

#### Development

```bash
npm install
```

## Usage

### Backend

#### Development

```bash
php artisan serve
```

### Frontend

#### Production

```bash
npm run build
```

#### Development

```bash
npm run dev
```

## Test

### Unit and Feature tests

```bash
php artisan test
```

### Coverage

```bash
XDEBUG_MODE=coverage php artisan test --coverage
```
