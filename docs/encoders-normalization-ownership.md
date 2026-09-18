# Кто знает, нужна ли кодеру нормализация

Исследование от 2026-09-16. В коде ничего не менялось.

## Вопросы

1. Кто знает, с какими данными работает кодер, с нормализованными или нет: фабрика, кодер или какой-то третий слой?
2. Верно ли, что на пути от возврата контроллера до выбора кодера перебираются сразу оба варианта значения, сырое и нормализованное?

## Вывод

1. **Это знание кодера.**
2. **Перебор обоих вариантов неверен.**

## Текущий путь в коде

- Контроллер → `ResponsesBuilder::body($body, ...$contentTypes)` → `BodiesFactory::create()` → `Responses` → `Negotiator`.
- `BodiesFactory::create()` ([BodiesFactory.php:40-44](../src/Http/Message/Bodies/BodiesFactory.php#L40-L44)):
  - нормализует значение всегда;
  - для каждого content type отбирает кодеры по content type;
  - собирает тела сначала из нормализованного значения, потом из сырого, и берёт первое.
- `BodyFactory::create()`:
  - отбирает кодеры по условию `encodesType($body) || encodesType($normalizedBody)`, затем по content type, и берёт первый;
  - `createBody()` ([BodyFactory.php:64](../src/Http/Message/Body/BodyFactory.php#L64)) передаёт кодеру нормализованное значение, если кодер его принимает, иначе сырое.

Что проверяет `encodesType` у каждого кодера:

| Кодер | Что принимает | Глубина проверки |
|---|---|---|
| `Encoder` (любой media type) | `string` | — |
| `Json\Encoder` | всё, кроме ресурсов и объектов, не являющихся `stdClass` | только верхний уровень |
| `Form\Encoder` | `array` \| `stdClass` | только верхний уровень |
| `Multipart\Encoder` | `array` \| `stdClass` без объектов и ресурсов внутри | рекурсивно |
| `Query\Encoder` | `array` \| `stdClass` | только верхний уровень |
| `View\Encoder` | `ViewInterface` | — |

Нормализаторы в контейнере плагина идут в таком порядке: `BackedEnum`, `DateTime` (формат ATOM), `View`, `Array`, `Object`. `Object` срабатывает на любой объект и собирает `stdClass` из `get_object_vars`.

## Почему это знание кодера

- **Его определяет реализация `encode()`.** `Json\Encoder` вызывает `json_encode`, и ему нужны нормализованные данные. `View\Encoder` вызывает `RendererInterface::render()`, и ему нужен `ViewInterface`.
- **Оно одно на весь класс кодера.** Не зависит ни от значения, ни от набора нормализаторов: JSON-кодер получает нормализованное значение всегда, View-кодер всегда получает доменный объект.
- **Значение это знание нести не может.** Один и тот же View уходит в `text/html` сырым, а в json нормализованным.
- **Фабрика это знание только использует.** Без кодера ей неоткуда его взять.

### Отброшенные доводы

- **«Ответ зависит от пары значение–кодер».** Довод верен для любых данных, поэтому ничего не доказывает. На деле ответ зависит только от кодера.
- **«Свойство меняется, если поменять нормализаторы».** Довод опирался на `View\Normalizer`, которого в коде нет (см. ниже). Кроме того, нормализатор, возвращающий значение без изменений, меняет только ярлык: View-кодер всё равно получает доменный объект.
- **«Нормализованное важнее — это политика фабрики».** Нет, это обходной путь. Фабрика не знает, какой вход нужен кодеру, и выясняет это перебором.

## Как это решено в других фреймворках

| Фреймворк | Где знание | Как выбирается путь | Перебор значений |
|---|---|---|---|
| Symfony Serializer | у кодера, маркер `NormalizationAwareInterface` | кодер выбирается по формату, затем `needsNormalization($format)` | нет |
| Spring MVC | в слое обработки результата контроллера, по его типу | `View`, `ModelAndView`, имя вида → `ViewResolver`; `@ResponseBody`, `ResponseEntity` → `HttpMessageConverter` | нет |
| ASP.NET Core | в типе результата действия | `ObjectResult` → output formatters; рендер вида идёт отдельным путём | нет |
| JAX-RS | у writer'а, отдельной нормализации нет | `MessageBodyWriter.isWriteable(type, genericType, annotations, mediaType)` | нет |

### Symfony

Схема та же, что у нас: сначала нормализаторы, потом кодеры, и часть кодеров принимает сырые данные. Из описания класса `Serializer`:

> objects are turned into arrays by normalizers. arrays are turned into various output formats by encoders.

Из `NormalizationAwareInterface`:

> Implementing this interface essentially just tells the Serializer that the data should not be pre-normalized before being passed to this Encoder.

`Serializer::serialize()`:

```php
if ($this->encoder->needsNormalization($format, $context)) {
    $data = $this->normalize($data, $format, $context);
}

return $this->encode($data, $format, $context);
```

`ChainEncoder::needsNormalization()` находит кодер только по формату и возвращает `false`, если кодер реализует `NormalizationAwareInterface`. Путь всегда один.

### Spring MVC

- Разделение «рендер или данные» происходит до выбора конвертера и определяется типом результата контроллера. `View` до `HttpMessageConverter` не доходит вообще.
- Content type для видов согласуется внутри слоя представлений: `ContentNegotiatingViewResolver` опрашивает свои резолверы и возвращает «most compatible view». JSON-представление в этой схеме тоже является видом.

### ASP.NET Core

POCO заворачивается в `ObjectResult`, а ответ формирует «first registered output formatter that can handle the response type». Рендер вида идёт отдельным путём, мимо output formatters.

## Почему третий вариант к нам не подходит

Вариант Spring и ASP.NET (развилка по типу результата до кодеров) работает, только пока рендер не является кодером. У нас `View\Encoder` участвует в согласовании content type наравне с JSON.

Этот вариант у нас уже был: `represent()` в [AdminAjax/Route.php:108](../src/Routes/AdminAjax/Route.php#L108) рендерил View для html ([строка 124](../src/Routes/AdminAjax/Route.php#L124)), а для остальных типов отдавал модель кодерам. Его заменили `View\Encoder`.

Вариант «решает контроллер» (`withBody` / `withUnnormalizedBody`) в текущей правке убран. Контроллер не знает, какой content type выиграет согласование.

## Почему перебор обоих вариантов неверен

Сейчас выбор делает порядок в `merge()` у `BodiesFactory` и правило «нормализованное важнее» в `BodyFactory::createBody()`, а не кодер.

- **Проба нормализованного ничего не проверяет.** Нормализованное значение принимает любой кодер, кроме View.
- **Проба сырого пропускает лишнее.** У `Json\Encoder` и `Form\Encoder` проверяется только верхний уровень. Для `['date' => new DateTime]` у JSON получается два кандидата, и сырой отбрасывается только потому, что стоит вторым. Если поменять порядок в `merge()`, результат молча изменится: `json_encode` выдаст DateTime внутренними полями (`date`, `timezone_type`, `timezone`) вместо ATOM. Это штатное поведение PHP; пример не запускался, PHP в окружении не было.
- **Нормализация выполняется всегда.** В том числе для View, который уходит в `text/html`. С `Object`-нормализатором из такого View получается бессмысленный `stdClass`.

По схеме Symfony путь был бы один: кодер для content type сообщает, нужна ли ему нормализация, и `encodesType` проверяет одно значение. Если на один content type подходит несколько кодеров (базовый `Encoder` принимает любой media type), проверка остаётся одна на кодер, и какое значение проверять, решает сам кодер.

## Попутно найдено (2026-09-16)

- `Http\Normalizers\View\Normalizer` зарегистрирован в `wordpress-plugin/container/definitions/http.php` (строки 20 и 27). Во фреймворке этого класса нет, и в git-истории его никогда не было, поэтому сборка `Normalizers` в контейнере упадёт.
- `Routes/AdminAjax/Route.php`, `Routes/Rest/Route.php` и `Routes/Feed/Route.php` ссылаются на несуществующие `Http\Coders` и `Http\Responder`, а `AdminAjax` ещё и на старое API `ResponseBuilder` (`withStatusCode`, `withBody`).

## Источники

- [Symfony `Serializer.php`](https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/Serializer/Serializer.php)
- [Symfony `ChainEncoder.php`](https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/Serializer/Encoder/ChainEncoder.php)
- [Symfony `NormalizationAwareInterface.php`](https://github.com/symfony/symfony/blob/7.3/src/Symfony/Component/Serializer/Encoder/NormalizationAwareInterface.php)
- [Spring MVC: возвращаемые значения](https://docs.spring.io/spring-framework/reference/web/webmvc/mvc-controller/ann-methods/return-types.html)
- [Spring `ContentNegotiatingViewResolver`](https://docs.spring.io/spring-framework/docs/current/javadoc-api/org/springframework/web/servlet/view/ContentNegotiatingViewResolver.html)
- [ASP.NET Core: форматирование ответа](https://learn.microsoft.com/en-us/aspnet/core/web-api/advanced/formatting)
- [Jakarta REST `MessageBodyWriter`](https://jakarta.ee/specifications/restful-ws/3.1/apidocs/jakarta.ws.rs/jakarta/ws/rs/ext/messagebodywriter)
