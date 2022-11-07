# Octopus Next

## Requirements

- [Docker](https://docs.docker.com/engine/install/)
- [Laravel Sail](https://laravel.com/docs/9.x/sail#installation)

## Installation

### Backend

```bash
git clone git@github.com:esl51/octopus-next.git ./
sail up -d
sail composer update
cp .env.example .env
sail artisan key:generate --ansi
sail artisan jwt:secret --force --ansi
mkdir -p storage/app/public/media
sail artisan storage:link
```
Edit `.env` and set your database connection details
```bash
sail artisan migrate:fresh --seed
```

### Frontend

#### Production

```bash
sail npm install
```

#### Development

```bash
npm install
```

## Usage

### Backend

```bash
sail up -d
```

### Frontend

#### Production

```bash
sail npm run build
```

#### Development

```bash
npm run dev
```

## Test

### Unit and Feature tests

```bash
sail artisan test
```

### Coverage

```bash
sail artisan test --coverage-html ./coverage
```
