# Тело в строителях HTTP

Статус: согласовано 2026-09-12, дополнено 2026-09-13 (шаблоны; тело, тип и статус), не реализовано.
Затрагивает `RequestBuilder`, `ResponseBuilder`, `ResponsesBuilder`, `BodyFactory`, нормализаторы,
`View` / `ViewFactory` / `Renderer`, HTML-кодер, `Response`, `Emitter`.

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
| `RequestBuilder`   | method, url       | значение без типа — исключение, `null` — без тела       | кодируем в него, в том числе `null`                |
| `ResponseBuilder`  | status code       | значение без типа — исключение, `null` — без тела       | кодируем в него, в том числе `null`                |
| `ResponsesBuilder` | status code, body | все кодеры, принявшие значение; ни одного — исключение  | только заданные; нет кодера для типа — исключение  |

Детали:

- Первый параметр `withContentTypes` обязателен, иначе `withContentTypes()` дублировал бы
  `withoutContentTypes()`.
- В одиночных строителях наличие тела определяется типом, см. п. 4. Флага нет.
- Content-Type ответа с телом всегда берётся из `$body->mediaType()` в конструкторе `Response`.
- `NormalizersInterface` строителям не нужен — фабрика нормализует сама.

### 2. Фабрика

- `createBodies(mixed $body, MediaTypeInterface|string ...$contentTypes): array` — без типов
  раскладка по всем кодерам, с типами — по телу на каждый. Значение нормализуется один раз.
- `createBody` / `createBodies` всегда нормализуют; `createBodyFromUnnormalized` /
  `createBodiesFromUnnormalized` удаляются.
- **Кодер получает нормализованное значение, а сырое — только если нормализованное он не принимает.**
  Проходные нормализаторы и правило «проходной раньше `Object`» уходят. Список исключений больше
  не ведётся: исключение — любой тип, который кодер принимает сырым и не принимает нормализованным.

### 3. `RenderableInterface` и `TemplateResolver`

```php
interface RenderableInterface
{
	public function template(): string;
}
```

- Заменяет `ViewInterface` (`file()` + `model()`). `ModelInterface` уходит: моделью становится сам view.
- View — `readonly` DTO. `public` — данные для всех форматов, `protected` — только для шаблона.
- View называет шаблон логическим именем (`'products.show'`), а не файлом. Шаблон, выбираемый
  во время выполнения, — `protected string $template` в конструкторе, в JSON он не попадает.
- `ViewFactory` и класс `View` удаляются. Фабрика не может собрать DTO с его собственным
  типизированным конструктором через общий `create(string $view, ModelInterface $model)`,
  а базовый класс больше не нужен.
- Обязанности фабрики и конструктора `View` переезжают в `TemplateResolver`, который получает
  `Renderer`: корень шаблонов `$dir` из DI, соглашение `products.show` → `products/show.php`,
  проверка пути (realpath, `is_file`, файл внутри корня).
- `Renderer` разрешает имя через резолвер и подключает шаблон в области видимости view,
  в шаблоне — `$this->...`.
- HTML-кодер принимает `RenderableInterface`.

Так один источник обслуживает несколько форматов: JSON получает публичные поля, HTML рендерит
шаблон с доступом ко всему объекту.

### 4. Тело, тип и статус

Кто за что отвечает:

- **Строитель** ничего не проверяет в сообщении, только собирает его. Исключения — отсутствующие
  обязательные части и значение, которое нечем закодировать.
- **`Response`** проверяет в конструкторе, возможно ли такое сообщение: пара «статус — тело»
  и Content-Type без тела. `withStatusCode()`, `withHeaders()`, `withBody()`, `withoutBody()` идут
  через `new static(...)`, поэтому инвариант держится и после middleware.
- **`Emitter`** выбрасывает тело для HEAD. Метод — свойство обмена, а не ответа, `Response` его не знает.

Одиночный строитель, наличие тела равно наличию типа:

| `body`                 | `withContentType` | результат                      |
|------------------------|-------------------|--------------------------------|
| `BodyInterface`        | любой             | тело как есть                  |
| любое, включая `null`  | задан             | `createBody($type, $body)`     |
| `null`                 | не задан          | без тела                       |
| не `null`              | не задан          | исключение: нечем кодировать   |

- Тип — это `withContentType`, а не заголовок. Content-Type в `withHeaders` — только метаданные
  и тела не создаёт.
- `withoutBody()` сбрасывает значение в `null`. При заданном типе получится закодированный `null`,
  тело убирает `withoutContentType()`.

`Response` всегда описывает ответ как на GET:

| статус    | тело      | Content-Type без тела | RFC 9110                                                        |
|-----------|-----------|-----------------------|-----------------------------------------------------------------|
| 1xx       | запрещено | —                     | §6.4.1; финальным ответом быть не может                         |
| 204       | запрещено | допустим              | §15.3.5: заголовки описывают выбранное представление            |
| 205       | запрещено | запрещён              | §15.3.6: MUST NOT generate content                              |
| 304       | запрещено | допустим              | §15.4.5: SHOULD NOT, кроме метаданных для обновления кеша        |
| остальные | можно     | запрещён              | §6.4.1: содержимое есть всегда, без тела — пустое, описывать нечего |

`Emitter` для HEAD отправляет статус и заголовки ответа как есть, тело не отправляет (§9.3.2).

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
- Имя шаблона, а не файл: view ничего не знает о файловой системе, правило «имя → файл» и проверка
  пути живут в одном месте. Переопределение шаблонов темой, если понадобится, добавляется в резолвер
  без правки вьюх. Без базового класса нет риска забытого `parent::__construct()`.
- Тело по типу, а не флаг: флаг нужен был только чтобы отличить `withBody(null)` от `withoutBody()`.
  Когда тип задан, `null` кодируется, и JSON `null` собирается. Без типа кодировать нечем,
  `null` честно означает «тела нет».
- Правило «тело = тип» взято не из RFC. §8.3 допускает Content-Type и без содержимого: он описывает
  «the representation enclosed in the message content or the selected representation». Спека
  обязывает только в обратную сторону: есть содержимое — SHOULD Content-Type. Правило — решение
  строителя, чтобы убрать флаг.
- HEAD в эмиттере, а не в `Response`. Ответ на HEAD возможен с любым статусом и SHOULD нести
  заголовки GET, в том числе Content-Type (§9.3.2). Если моделировать его в `Response` как ответ
  без тела, `Response` пришлось бы разрешить тип без тела при любом статусе, и он перестал бы
  ловить 200 с `application/json` и пустым содержимым. Заодно заголовки HEAD и GET совпадают
  сами, а middleware, считающее что-то по телу, работает одинаково для обоих методов.
- Проверки в `Response`, а не в строителе: инвариант сообщения должен держаться для любого способа
  создать ответ, в том числе для `Response::with*` в middleware.

Аналоги: в ядре Symfony нет. Ближе всего JAX-RS — одна сущность в `Response.ok(entity)`,
`MessageBodyWriter` выбирается по типу сущности и медиатипу, `@Produces` сужает набор.

## Опорные факты (проверено 2026-09-12 и 2026-09-13)

- `Response::__construct` ставит Content-Type из `$body->mediaType()`, если тело есть.
- `Object\Normalizer` берёт свойства через `get_object_vars()` из своей области видимости —
  видит только публичные, `protected`-свойства (в том числе `$template`) в JSON не попадают.
- `ViewFactory` держит `$dir` из DI и превращает точки в имени в разделители; конструктор `View`
  проверяет realpath корня и файла, `is_file` и что файл лежит внутри корня.
- `Encoders::first()` бросает `no encoder found`; `EncodersInterface::filter(Closure)` публичный.
- `BodyFactory::createBodyFromUnnormalized` уже выбирает кодер по нормализованному значению.
- `ViewInterface` помимо `Renderer`, HTML-кодера и `ViewFactory` используют `Hooks/Action/Hook`,
  `Hooks/Filter/Hook`, `Routes/AdminAjax/Route`.
- `Response::__construct` без тела оставляет заголовки как есть, в том числе Content-Type из `withHeaders`;
  проверяет только диапазон 100–599. `Response::withoutBody()` сохраняет заголовки, включая
  Content-Type прежнего тела.
- `Emitter::emit(ResponseInterface)` делает `echo $response->body()` без условий, метода запроса не знает.
- Базовый `Encoder::encodesType` (`text/*`) принимает только строки, JSON-кодер — всё, кроме ресурсов.
  По коду, не запускалось: `createBody('text/html', null)` упадёт на `no encoder found`,
  `createBody('application/json', null)` даст `null`.

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

	public function template(): string
	{
		return 'products.show';
	}
}

return $this->responsesBuilder
	->withStatusCode(200)
	->withContentTypes('text/html', 'application/json')
	->withBody(new ProductView($product->title, $product->price, wp_create_nonce('buy')))
	->build();
```

JSON получит `{"title": ..., "price": ...}`, HTML отрендерит `products/show.php` из корня шаблонов,
где доступен и `$this->nonce`.

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
		if ($this->body instanceof BodyInterface) {
			return $this->body;
		}

		if ($this->contentType === null) {
			if ($this->body !== null) {
				throw new ResponseBuilderException('content type is mandatory to encode a body');
			}

			return null;
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

### `TemplateResolver` и `Renderer`

```php
readonly class TemplateResolver implements TemplateResolverInterface
{
	public function __construct(
		protected string $dir,
	) {
	}

	public function resolve(string $template): string
	{
		$dir = realpath($this->dir);
		if ($dir === false) {
			throw new TemplateResolverException('dir not found');
		}

		$file = realpath($dir . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $template) . '.php');
		if ($file === false) {
			throw new TemplateResolverException('file not found');
		}

		if (!is_file($file)) {
			throw new TemplateResolverException('not a file');
		}

		if (!str_starts_with($file, $dir . DIRECTORY_SEPARATOR)) {
			throw new TemplateResolverException('file outside dir');
		}

		return $file;
	}
}
```

`Renderer` получает `TemplateResolverInterface $templateResolver` в конструкторе:

```php
	public function render(RenderableInterface $view): string
	{
		$escaper = $this->escaper;
		$file = $this->templateResolver->resolve($view->template());

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
- `ViewFactory::create()` для новых вьюх — не может собрать DTO с типизированным конструктором.
- `file()` с `__DIR__` в каждой вьюхе или базовый `View` с `protected $file` — view знает о файловой
  системе, проверка пути либо пропадает, либо требует наследования с `parent::__construct()`, забытый
  вызов которого падает только на рендере.
- Флаг `hasBody` в одиночных строителях, чтобы различать `withBody(null)` и `withoutBody()`, —
  лишнее поле во всех `new static(...)`, различие закрыто правилом «тело = тип».
- `null` как «нет тела» независимо от типа — JSON `null` из сырого значения не собрать.
- Маркер отсутствия (`enum`) вместо флага — тот же флаг, видимый в сигнатуре конструктора и пресетах DI.
- Проверки сообщения в `build()` строителя — не покрывают `Response::with*` в middleware.
- HEAD как `Response` без тела, но с типом — `Response` вынужден разрешить тип без тела при любом
  статусе, заголовки HEAD и GET расходятся, см. «Почему».

## Цена, принятая осознанно

- Ошибки фабрик (кривой URL, медиатип, заголовки) вылетают в `build()`, стек указывает на `build()`,
  а не на `with*`.
- Ошибка «шаблон не найден» вылетает при рендере, а не при создании view (сейчас её ловит конструктор
  `View` ещё в маршруте), то есть после отправки статуса и заголовков. Лечится незакрытым пунктом
  решения по кодированию тела: в `Emitter::emit()` сначала `(string) $response->body()`, потом заголовки.
- Для HEAD тело генерируется и выбрасывается. §9.3.2 называет это менее предпочтительным, чем
  расхождение заголовков, но не запрещает.
- Строитель с пресетом типа и `withStatusCode(204)` соберёт тело, и `Response` бросит. Нужен
  `withoutContentType()`; Content-Type-метаданные для 204/304 — через `withHeaders`.
- `withoutBody()` при заданном типе не убирает тело, а даёт закодированный `null`; с `text/*` это
  исключение кодера.

## Открыто и учесть при реализации

1. **«Не заданы — все» опасно в маршрутах.** Забытый `withContentTypes` не падает, а формат ответа
   начинает зависеть от глобального реестра кодеров: новый кодер молча добавляет формат во все
   маршруты без явных типов. `withoutContentTypes()` в обработчике читается как «без типов»,
   а значит «все». Смягчение без смены правила — конфигурацией: строитель для маршрутов приходит
   из DI с типами, обработчику — отдельный экземпляр без них.
2. **Content-Type в двух местах** — `withContentType` и `withHeaders(['content-type' => ...])`.
   Смысл разведён (п. 4): тип — «закодируй тело», заголовок — метаданные. Остаётся случай, когда
   есть и тело, и заголовок: `Response` молча перезаписывает заголовок типом тела. Бросать ли
   в `Response` при расхождении — не решено; в строителе не проверяем.
3. **Пресеты и `withHeaders`, заменяющий целиком.** Строитель из DI с `cache-control` потеряет его
   на `withHeaders(['authorization' => ...])`. Нужен добавочный `withHeader(string $name, string $value)`.
4. **Правило «public — данные, protected — только шаблон» ничем не защищено.** Поле для шаблона,
   сделанное публичным по привычке, молча уедет в JSON.
5. **HTML-страницы платят проход нормализатора по view.** Раньше проходной нормализатор это экономил;
   на страницах с большими списками будет заметно.
6. **Правка тела через accessor в middleware меняет только JSON.** HTML-тело держит объект, а не
   `Accessor\Body`. Проверить, есть ли middleware или хуки, меняющие данные ответа.
7. **Идемпотентность нормализаторов** на уже нормализованных массивах и скалярах — сегодняшние
   вызовы `withBody` передают готовые данные. Цена — лишний проход по большим массивам (фиды).
8. **Мелочи DX:** `withBody(mixed)` не показывает `BodyInterface` — докблок `@param BodyInterface|mixed`;
   `withStatusCode(int)` — единственная часть без пары «VO|примитив»; по строке `'products.show'`
   IDE не переходит к файлу шаблона.
9. **Общий абстрактный класс** для `ResponseBuilder` и `ResponsesBuilder` — тогда позиционный
   `new static(...)` станет общим.
10. **Миграция:** `ViewInterface` → `RenderableInterface` в `Renderer`, HTML-кодере, `Hooks/Action/Hook`,
    `Hooks/Filter/Hook`, `Routes/AdminAjax/Route`; `ViewFactory` и `View` удалить, `$dir` перенести
    в DI-определение `TemplateResolver`; шаблоны `$model->` → `$this->`; DI-определения строителей
    и нормализаторов (проходной для view убрать).
11. **Эмиттеру нужен метод запроса** для HEAD — меняется сигнатура `emit()`: запрос или метод.
12. **`Response::withoutBody()` сохраняет Content-Type прежнего тела** — при статусах из «остальных»
    `Response` бросит. Решить: `withoutBody()` убирает и тип, или это забота вызывающего.
13. **`ResponsesBuilder` не пересмотрен:** набросок держит `hasBody` для «body обязателен»,
    правило «тело = тип» к нему не переносится — без типов там «все кодеры».
14. **Политика для `Request`** (метод — тело, тип без тела) не обсуждалась; правило строителя
    в таблице п. 1 записано для обоих одиночных строителей.
