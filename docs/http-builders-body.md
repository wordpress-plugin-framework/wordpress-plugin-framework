# Тело в строителях HTTP

Статус: согласовано 2026-09-12, не реализовано.
Затрагивает `RequestBuilder`, `ResponseBuilder`, `ResponsesBuilder`, `BodyFactory`, нормализаторы,
`View` / `Renderer`, HTML-кодер.

## Решение

### 1. Канонические строители

Все `with*` только сохраняют аргумент в том виде, в каком он пришёл (VO или примитив).
Фабрики (`Method::create`, `UrlFactory`, `HeadersFactory`, `BodyFactory`) вызываются в `build()`.

Тип содержимого — отдельная часть строителя, тело — отдельная:

- `RequestBuilder`, `ResponseBuilder`: `withContentType(MediaTypeInterface|string $contentType)`,
  `withoutContentType()`.
- `ResponsesBuilder`: `withContentTypes(MediaTypeInterface|string $contentType, MediaTypeInterface|string ...$contentTypes)`,
  `withoutContentTypes()`.
- Все три: `withBody(mixed $body)`, `withoutBody()`. `BodyInterface` проходит как есть,
  заданный тип к нему не применяется.
- Удаляются `withUnnormalizedBody`, `withBodies`, `withUnnormalizedBodies`, `withoutBodies`.

Что проверяет `build()`:

| строитель          | обязательно       | тип не задан                                            | тип задан                                          |
|--------------------|-------------------|---------------------------------------------------------|----------------------------------------------------|
| `RequestBuilder`   | method, url       | тело без типа — исключение                              | кодируем в него                                    |
| `ResponseBuilder`  | status code       | тело без типа — исключение                              | кодируем в него                                    |
| `ResponsesBuilder` | status code, body | все кодеры, принявшие значение; ни одного — исключение  | только заданные; нет кодера для типа — исключение  |

Детали:

- `withBody(null)` и `withoutBody()` различаются флагом `hasBody`.
- Первый параметр `withContentTypes` обязателен, иначе `withContentTypes()` дублировал бы
  `withoutContentTypes()`.
- `withoutContentType()` в одиночных строителях — ради зеркальности, функционально это только
  сброс типа, заданного пресетом.
- Content-Type ответа всегда берётся из `$body->mediaType()` в конструкторе `Response`.
- `NormalizersInterface` строителям не нужен — фабрика нормализует сама.

### 2. Фабрика

- `createBodies(mixed $body, MediaTypeInterface|string ...$contentTypes): array` — без типов
  раскладка по всем кодерам, с типами — по телу на каждый. Значение нормализуется один раз.
- `createBody` / `createBodies` всегда нормализуют; `createBodyFromUnnormalized` /
  `createBodiesFromUnnormalized` удаляются.
- **Кодер получает нормализованное значение, а сырое — только если нормализованное он не принимает.**
  Проходные нормализаторы и правило «проходной раньше `Object`» уходят. Список исключений больше
  не ведётся: исключение — любой тип, который кодер принимает сырым и не принимает нормализованным.

### 3. `RenderableInterface`

```php
interface RenderableInterface
{
	public function file(): string;
}
```

- Заменяет `ViewInterface` (`file()` + `model()`). `ModelInterface` уходит: моделью становится сам view.
- View — `readonly` DTO. `public` — данные для всех форматов, `protected` — только для шаблона.
- Шаблон постоянный для класса — `file()` реализуется прямо. Базовый `View` с `protected $file`
  и проверкой пути — только когда шаблон выбирается во время выполнения.
- `Renderer` подключает шаблон в области видимости view, в шаблоне — `$this->...`.
- HTML-кодер принимает `RenderableInterface`.

Так один источник обслуживает несколько форматов: JSON получает публичные поля, HTML рендерит
шаблон с доступом ко всему объекту.

## Почему

- `withBodies(Body|mixed ...$bodies)` смешивает две оси: вариадик — «N источников»,
  фабрика — «один источник → N тел». Кардинальность элемента плавает (`Body` → 1,
  `mixed` → N), имя врёт.
- RFC 9110: у ресурса одно состояние и много представлений, согласуются представления.
  Пользователь отдаёт состояние, представления — забота строителя.
- Канонический строитель: порядок вызовов не важен, работают пресеты из DI.
  Сейчас `withBody()` читает Content-Type в момент вызова: `withBody($x)->withHeaders([...])`
  бросает, а `withHeaders(json)->withBody($x)->withHeaders(xml)` молча собирает тело под JSON
  с заголовком XML.
- Тип — отдельная часть, а не пара `withBody($body, $type)`: у пары VO и примитив разной арности
  и не помещаются в одну сигнатуру, а недопустимые сочетания (`BodyInterface` + тип, сырое
  значение без типа) ловятся только проверками в `withBody`. С отдельной частью в `build()`
  остаются проверки обязательности, а тип из пресета вместе с готовым телом — законное сочетание.
- «Не заданы — все»: раскладка по всем кодерам нужна обработчику исключений, в маршрутах типы
  задаются явно или приходят пресетом.
- Выбор входа в фабрике, а не проходные нормализаторы: одно нормализованное значение не может
  быть одновременно объектом для HTML и данными для JSON.
- `readonly` у view: HTML-тело держит объект, JSON-тело — нормализованную копию; изменение
  после `build()` развело бы представления.

Аналоги: в ядре Symfony нет. Ближе всего JAX-RS — одна сущность в `Response.ok(entity)`,
`MessageBodyWriter` выбирается по типу сущности и медиатипу, `@Produces` сужает набор.

## Опорные факты (проверено 2026-09-12)

- `Response::__construct` ставит Content-Type из `$body->mediaType()`, если тело есть.
- `Object\Normalizer` берёт свойства через `get_object_vars()` из своей области видимости —
  видит только публичные, `protected $file` в JSON не попадает.
- `Encoders::first()` бросает `no encoder found`; `EncodersInterface::filter(Closure)` публичный.
- `BodyFactory::createBodyFromUnnormalized` уже выбирает кодер по нормализованному значению.
- `ViewInterface` помимо `Renderer`, HTML-кодера и `ViewFactory` используют `Hooks/Action/Hook`,
  `Hooks/Filter/Hook`, `Routes/AdminAjax/Route`.

## DX

```php
interface RequestBuilderInterface
{
	public function withMethod(Method|string $method): static;
	public function withUrl(UrlInterface|string $url): static;
	public function withHeaders(HeadersInterface|array $headers): static;

	public function withContentType(MediaTypeInterface|string $contentType): static;
	public function withoutContentType(): static;

	public function withBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): RequestInterface;
}

interface ResponseBuilderInterface
{
	public function withStatusCode(int $statusCode): static;
	public function withHeaders(HeadersInterface|array $headers): static;

	public function withContentType(MediaTypeInterface|string $contentType): static;
	public function withoutContentType(): static;

	public function withBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): ResponseInterface;
}

interface ResponsesBuilderInterface
{
	public function withStatusCode(int $statusCode): static;
	public function withHeaders(HeadersInterface|array $headers): static;

	public function withContentTypes(MediaTypeInterface|string $contentType, MediaTypeInterface|string ...$contentTypes): static;
	public function withoutContentTypes(): static;

	public function withBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): ResponsesInterface;
}
```

Запрос из HTTP-клиента:

```php
$request = $this->requestBuilder
	->withMethod('POST')
	->withUrl('https://api.example.com/orders')
	->withHeaders(['authorization' => "Bearer {$token}"])
	->withContentType('application/json')
	->withBody($order)
	->build();
```

Одиночный ответ:

```php
return $this->responseBuilder
	->withStatusCode(201)
	->withContentType('application/json')
	->withBody($product)
	->build();
```

Согласуемый ответ в маршруте, только заданные типы, один источник на HTML и JSON:

```php
readonly class ProductView implements RenderableInterface
{
	public function __construct(
		public string $title,
		public float $price,
		protected string $nonce,
	) {
	}

	public function file(): string
	{
		return __DIR__ . '/templates/product.php';
	}
}

return $this->responsesBuilder
	->withStatusCode(200)
	->withContentTypes('text/html', 'application/json')
	->withBody(new ProductView($product->title, $product->price, wp_create_nonce('buy')))
	->build();
```

JSON получит `{"title": ..., "price": ...}`, HTML отрендерит `product.php`, где доступен и `$this->nonce`.

Обработчик исключений, все кодеры, принявшие значение:

```php
return $this->responsesBuilder
	->withStatusCode(500)
	->withBody($problem)
	->build();
```

Строитель из DI с пресетом `withContentTypes('application/json')`:

```php
// маршрут — тип не повторяется
return $this->responsesBuilder
	->withStatusCode(200)
	->withBody($products)
	->build();

// сброс на «все»
return $this->responsesBuilder
	->withoutContentTypes()
	->withStatusCode(500)
	->withBody($problem)
	->build();
```

Готовое тело:

```php
return $this->responseBuilder
	->withStatusCode(200)
	->withBody($upstream->body())
	->build();
```

## Набросок реализации

### `ResponsesBuilder`

```php
readonly class ResponsesBuilder implements ResponsesBuilderInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		protected HeadersInterface|array $headers = [],
		protected array $contentTypes = [],
		protected bool $hasBody = false,
		protected mixed $body = null,
	) {
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->contentTypes, $this->hasBody, $this->body);
	}

	public function withHeaders(HeadersInterface|array $headers): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->contentTypes, $this->hasBody, $this->body);
	}

	public function withContentTypes(MediaTypeInterface|string $contentType, MediaTypeInterface|string ...$contentTypes): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, [$contentType, ...$contentTypes], $this->hasBody, $this->body);
	}

	public function withoutContentTypes(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, [], $this->hasBody, $this->body);
	}

	public function withBody(mixed $body): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $this->contentTypes, true, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $this->contentTypes, false, null);
	}

	public function build(): ResponsesInterface
	{
		if ($this->statusCode === null) {
			throw new ResponsesBuilderException('status code is mandatory');
		}

		$headers = $this->headers();
		$responses = new Responses();

		foreach ($this->bodies() as $body) {
			$response = new Response($this->uuid, $this->statusCode, $headers, $body);

			$responses = $responses->with($response);
		}

		return $responses;
	}

	protected function headers(): HeadersInterface
	{
		return $this->headers instanceof HeadersInterface
			? $this->headers
			: $this->headersFactory->create($this->headers);
	}

	protected function bodies(): array
	{
		if (!$this->hasBody) {
			throw new ResponsesBuilderException('building representations without body is prohibited');
		}

		if ($this->body instanceof BodyInterface) {
			return [$this->body];
		}

		$bodies = $this->bodyFactory->createBodies($this->body, ...$this->contentTypes);
		if ($bodies === []) {
			throw new ResponsesBuilderException('no encoder accepts the body');
		}

		return $bodies;
	}
}
```

### `ResponseBuilder`

Отличается одним `MediaTypeInterface|string|null $contentType` вместо массива,
`withContentType` / `withoutContentType` и методами `build()` / `body()`:

```php
	public function build(): ResponseInterface
	{
		if ($this->statusCode === null) {
			throw new ResponseBuilderException('status code is mandatory');
		}

		return new Response($this->uuid, $this->statusCode, $this->headers(), $this->body());
	}

	protected function body(): ?BodyInterface
	{
		if (!$this->hasBody) {
			return null;
		}

		if ($this->body instanceof BodyInterface) {
			return $this->body;
		}

		if ($this->contentType === null) {
			throw new ResponseBuilderException('content type is mandatory to encode a body');
		}

		return $this->bodyFactory->createBody($this->contentType, $this->body);
	}
```

`RequestBuilder` — то же, плюс `Method|string|null` и `UrlInterface|string|null`, которые
разрешаются в `build()` через `Method::create` и `UrlFactory`.

### `BodyFactory`

```php
	public function createBody(MediaTypeInterface|string $contentType, mixed $body): BodyInterface
	{
		$normalized = $this->normalizers->normalize($body);

		return $this->body($this->encoder($contentType, $body, $normalized), $body, $normalized);
	}

	public function createBodies(mixed $body, MediaTypeInterface|string ...$contentTypes): array
	{
		$normalized = $this->normalizers->normalize($body);

		if ($contentTypes !== []) {
			return array_map(
				fn(MediaTypeInterface|string $contentType) => $this->body($this->encoder($contentType, $body, $normalized), $body, $normalized),
				$contentTypes,
			);
		}

		$bodies = [];
		foreach ($this->encoders($body, $normalized) as $encoder) {
			$bodies[] = $this->body($encoder, $body, $normalized);
		}

		return $bodies;
	}

	protected function encoders(mixed $body, mixed $normalized): EncodersInterface
	{
		return $this->encoders->filter(
			fn(EncoderInterface $encoder) => $encoder->encodesType($normalized) || $encoder->encodesType($body),
		);
	}

	protected function encoder(MediaTypeInterface|string $contentType, mixed $body, mixed $normalized): EncoderInterface
	{
		$mediaType = $contentType instanceof MediaTypeInterface ? $contentType : $this->mediaTypeFactory->create($contentType);

		return $this->encoders($body, $normalized)
			->filterByMediaType($mediaType)
			->mapMediaType($mediaType)
			->first();
	}

	protected function body(EncoderInterface $encoder, mixed $body, mixed $normalized): BodyInterface
	{
		$input = $encoder->encodesType($normalized) ? $normalized : $body;

		return is_array($input) || $input instanceof stdClass
			? new Accessor\Body($this->accessor, $encoder, $input)
			: new Body($encoder, $input);
	}
```

### `Renderer`

```php
	public function render(RenderableInterface $view): string
	{
		$escaper = $this->escaper;
		$file = $view->file();

		ob_start();

		try {
			(function () use ($escaper, $file) {
				require $file;
			})->call($view);
		} catch (Throwable $throwable) {
			ob_end_clean();

			throw $throwable;
		}

		$ob = ob_get_clean();
		if ($ob === false) {
			throw new RendererException('Failed to capture view output');
		}

		return $ob;
	}
```

## Отвергнуто

- `withBodies(Body|mixed ...$bodies)` — смешение осей, см. «Почему».
- `withBodies(mixed)` с добавлением к накопленным телам — расходится с заменой во всех остальных `with*`.
- Тип из заголовка Content-Type и создание тела в `withBody` (исходное состояние) — зависимость
  от порядка вызовов, круг «заголовок → тело → заголовок в `Response`».
- `withBody($body, ?$contentType)` / `withBody($body, ...$contentTypes)` с `BodyInterface` в том же
  методе — недопустимые сочетания ловятся только проверками внутри `withBody`.
- То же с обязательным типом и без `BodyInterface` в строителе — проверок нет, но арность 2 ломает
  однородность частей, готовое тело уходит из строителя, пресетов нет. Его преимущество —
  несколько источников через слоты по медиатипу — закрыто `RenderableInterface`.
- Отдельная часть типа при немедленной сборке — возвращает зависимость от порядка вызовов.
- «Режима "все" в строителе нет, обработчик перечисляет типы сам» — обработчику нужна раскладка
  по всем кодерам.
- Проходные нормализаторы для view — не дают HTML объект, а JSON данные одновременно.
- View-обёртка с `$view->data()` — JSON-кодер должен знать про view.
- Склейка представлений через `Responses::with()` — дублирует статус и заголовки в двух строителях.

## Цена, принятая осознанно

- Ошибки фабрик (кривой URL, медиатип, заголовки) вылетают в `build()`, стек указывает на `build()`,
  а не на `with*`.

## Открыто и учесть при реализации

1. **«Не заданы — все» опасно в маршрутах.** Забытый `withContentTypes` не падает, а формат ответа
   начинает зависеть от глобального реестра кодеров: новый кодер молча добавляет формат во все
   маршруты без явных типов. `withoutContentTypes()` в обработчике читается как «без типов»,
   а значит «все». Смягчение без смены правила — конфигурацией: строитель для маршрутов приходит
   из DI с типами, обработчику — отдельный экземпляр без них.
2. **Content-Type в двух местах** — `withContentType` и `withHeaders(['content-type' => ...])`.
   При наличии тела заголовок молча перезаписывается в `Response`. Предложено бросать в `build()`,
   если в заголовках есть Content-Type и тело задано.
3. **Пресеты и `withHeaders`, заменяющий целиком.** Строитель из DI с `cache-control` потеряет его
   на `withHeaders(['authorization' => ...])`. Нужен добавочный `withHeader(string $name, string $value)`.
4. **Правило «public — данные, protected — только шаблон» ничем не защищено.** Поле для шаблона,
   сделанное публичным по привычке, молча уедет в JSON.
5. **HTML-страницы платят проход нормализатора по view.** Раньше проходной нормализатор это экономил;
   на страницах с большими списками будет заметно.
6. **Правка тела через accessor в middleware меняет только JSON.** HTML-тело держит объект, а не
   `Accessor\Body`. Проверить, есть ли middleware или хуки, меняющие данные ответа.
7. **Наследник базового `View` без `parent::__construct()`** оставляет `readonly $file`
   неинициализированным и падает на рендере — уже после отправки статуса и заголовков
   (порядок в `Emitter::emit()`, незакрытый пункт решения по кодированию тела).
8. **Идемпотентность нормализаторов** на уже нормализованных массивах и скалярах — сегодняшние
   вызовы `withBody` передают готовые данные. Цена — лишний проход по большим массивам (фиды).
9. **Мелочи DX:** `withBody(mixed)` не показывает `BodyInterface` — докблок `@param BodyInterface|mixed`;
   `withStatusCode(int)` — единственная часть без пары «VO|примитив».
10. **Общий абстрактный класс** для `ResponseBuilder` и `ResponsesBuilder` — тогда позиционный
    `new static(...)` станет общим.
11. **Миграция:** `ViewInterface` → `RenderableInterface` в `Renderer`, HTML-кодере, `ViewFactory`,
    `Hooks/Action/Hook`, `Hooks/Filter/Hook`, `Routes/AdminAjax/Route`; шаблоны `$model->` → `$this->`;
    DI-определения строителей и нормализаторов (проходной для view убрать).
