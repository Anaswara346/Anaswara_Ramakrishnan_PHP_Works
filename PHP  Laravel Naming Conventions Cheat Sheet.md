# PHP / Laravel Naming Conventions Cheat Sheet

| Code element         | Naming format               | Example                                     |
| -------------------- | --------------------------- | ------------------------------------------- |
| Class name           | `PascalCase`                | `EventController`                           |
| Model name           | `PascalCase`, singular      | `EventRegistration`                         |
| Controller name      | `PascalCase` + `Controller` | `EventController`                           |
| Function / method    | `camelCase`                 | `createEvent()`                             |
| Variable             | `camelCase`                 | `$eventName`                                |
| Property             | `camelCase`                 | `$eventStatus`                              |
| Constant             | `UPPER_SNAKE_CASE`          | `MAX_ATTEMPTS`                              |
| Interface            | `PascalCase`                | `PaymentGatewayInterface`                   |
| Trait                | `PascalCase`                | `HasUuid`                                   |
| Enum                 | `PascalCase`                | `EventStatus`                               |
| Enum case            | `PascalCase` (recommended)  | `Published`                                 |
| PHP class filename   | Same as class name          | `EventController.php`                       |
| Database table       | Plural `snake_case`         | `event_registrations`                       |
| Database column      | `snake_case`                | `created_at`                                |
| Foreign key          | `snake_case` + `_id`        | `event_id`                                  |
| Migration filename   | Timestamp + `snake_case`    | `2026_10_23_110000_create_events_table.php` |
| Folder / namespace   | Follow Laravel structure    | `app/Http/Controllers/`                     |
| Blade view filename  | `snake_case`                | `event_details.blade.php`                   |
| URL path             | `kebab-case` (recommended)  | `/event-registrations`                      |
| Route name           | Dot notation                | `events.index`                              |
| API JSON fields      | `snake_case` (recommended)  | `"event_id": 123`                           |
| Environment variable | `UPPER_SNAKE_CASE`          | `APP_DEBUG=true`                            |

## Simple example

```
<?php

namespace App\Models;

class EventRegistration
{
    public const MAX_ATTEMPTS = 3;

    public function getEventDetails(): array
    {
        $eventName = 'Tech Conference';

        return [
            'event_name' => $eventName,
        ];
    }
}
```

Remember these four rules:

- PascalCase → Classes and class filenames: `EventService.php`
- camelCase → Methods and variables: `getEvent()`, `$eventName`
- snake_case → Database tables, columns, and API fields: `event_registrations`
- UPPER_SNAKE_CASE → Constants: `MAX_ATTEMPTS`

Follow PSR-12 for PHP formatting and Laravel's conventions for framework-specific components.