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

- [Best practices](https://github.com/alexeymezenin/laravel-best-practices/blob/master/russian.md)
- Доработать модуль списка файлов для переименования файлов (внутри объектов, а не в разделе Файлы)
- Сортировка перетаскиванием файлов в списке
- Просмотр изображений/аватарок в модальном окне
- VSelect перевести на [vue-multiselect](https://vue-multiselect.js.org/)
- Перевести строки в VSelect [подробнее](https://github.com/sagalbot/vue-select/pull/988)
- Удалить helpers.cast когда [исправят](https://github.com/vuejs/language-tools/issues/3400)
