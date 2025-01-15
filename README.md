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

# Roadmap

- Освежить по [Best practices](https://github.com/alexeymezenin/laravel-best-practices/blob/master/russian.md)
- Просмотр изображений/аватарок в модальном окне
- Перевести строки в VSelect [подробнее](https://github.com/sagalbot/vue-select/pull/988)
- Удалить helpers.cast когда [исправят](https://github.com/vuejs/language-tools/issues/3400)
- Явно указывать, какие поля выбирать, чтоб не делать выборку всех колонок в таблице
