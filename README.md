# Balance API

Микросервис для управления балансом пользователей

## Требования
- Docker
- Docker Compose

## Стек

- PHP 8.4 + Laravel 12
- PostgreSQL 17
- Nginx
- Docker

## Старт
Инициализация: контейнеры, зависимости, .env, миграции, сид
```bash
make init
```

## Доступные команды

```bash
make init          # Полная инициализация проекта
make up            # Запуск контейнеров
make down          # Остановка контейнеров  
make restart       # Перезапуск контейнеров
make refresh       # Сброс БД и миграции
make test          # Запуск тестов
make cli           # Вход в PHP CLI контейнер
make clear-cache   # Очистка кэша Laravel
```

## URL

`http://localhost:8080`

## API Endpoints

- `POST /api/deposit` - Пополнение баланса
- `POST /api/withdraw` - Списание средств
- `POST /api/transfer` - Перевод между пользователями
- `GET /api/balance/{user_id}` - Получение баланса

### Пополнение баланса
**POST /api/deposit**
```json
{
  "user_id": 1,
  "amount": 500.00,
  "comment": "Пополнение через карту"
}
```

### Списание средств
**POST /api/withdraw**
```json
{
  "user_id": 1, 
  "amount": 200.00,
  "comment": "Покупка подписки"
}
```

### Перевод между пользователями
**POST /api/transfer**
```json
{
  "from_user_id": 1,
  "to_user_id": 2, 
  "amount": 150.00,
  "comment": "Перевод другу"
}
```

### Получение баланса
**GET /api/balance/1**
```json
{
  "status": "success",
  "user_id": 1,
  "balance": "350.00"
}
```

## Формат ответов

Успех:
```json
{
  "status": "success",
  "user_id": 1,
  "balance": "100.50"
}
```

Ошибка:
```json
{
  "status": "error", 
  "code": "insufficient_funds",
  "message": "Insufficient funds"
}
```

## Разработка

```bash
# Инициализация
make init
# Войти в CLI контейнер для прямого доступа к php artisan
make cli
```
