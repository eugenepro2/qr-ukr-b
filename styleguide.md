![Хорошие практики Laravel](https://raw.githubusercontent.com/alexeymezenin/laravel-best-practices/master/images/logo-russian.png)

Это не пересказ лучших практик вроде SOLID, паттернов и пр. с адаптацией под Laravel. Здесь собраны именно практики, которые игнорируются в реальных Laravel проектах. Также, рекомендую ознакомитсья с [хорошими практиками в контексте PHP](https://github.com/jupeter/clean-code-php). Смотрите также [обсуждение хороших практик Laravel](https://laravel.ru/forum/viewforum.php?id=17).

### **Принцип единственной ответственности (Single responsibility principle)**

Каждый класс и метод должны выполнять лишь одну функцию.

Плохо:

```
public function getFullNameAttribute()
{
    if (auth()->user() && auth()->user()->hasRole('client') && auth()->user()->isVerified()) {
        return 'Mr. ' . $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    } else {
        return $this->first_name[0] . '. ' . $this->last_name;
    }
}
```

Хорошо:

```
public function getFullNameAttribute()
{
    return $this->isVerifiedClient() ? $this->getFullNameLong() : $this->getFullNameShort();
}

public function isVerifiedClient()
{
    return auth()->user() && auth()->user()->hasRole('client') && auth()->user()->isVerified();
}

public function getFullNameLong()
{
    return 'Mr. ' . $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
}

public function getFullNameShort()
{
    return $this->first_name[0] . '. ' . $this->last_name;
}
```

### **Тонкие контроллеры, толстые модели**

По своей сути, это лишь один из частных случаев принципа единой ответственности. Выносите работу с данными в модели при работе с Eloquent или в репозитории при работе с Query Builder или "сырыми" SQL запросами.

Плохо:

```
public function index()
{
    $clients = Client::verified()
        ->with(['orders' => function ($q) {
            $q->where('created_at', '>', Carbon::today()->subWeek());
        }])
        ->get();

    return view('index', ['clients' => $clients]);
}
```

Хорошо:

```
//В контроллере
public function index()
{
    return view('index', ['clients' => $this->client->getWithNewOrders()]);
}
//В модели
class Client extends Model
{
    public function getWithNewOrders()
    {
        return $this->verified()
            ->with(['orders' => function ($q) {
                $q->where('created_at', '>', Carbon::today()->subWeek());
            }])
            ->get();
    }
}
```

### **Валидация**

Следуя принципам тонкого контроллера и SRP, выносите валидацию из контроллера в Request классы.

Плохо:

```
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
        'publish_at' => 'nullable|date',
    ]);

    ....
}
```

Хорошо:

```
public function store(PostRequest $request)
{    
    ....
}

class PostRequest extends Request
{
    public function rules()
    {
        return [
            'title' => 'required|unique:posts|max:255',
            'body' => 'required',
            'publish_at' => 'nullable|date',
        ];
    }
}
```

### **Бизнес логика в сервис-классах**

Контроллер должен выполнять только свои прямые обязанности, поэтому выносите всю бизнес логику в отдельные классы и сервис классы.

Плохо:

```
public function store(Request $request)
{
    if ($request->hasFile('image')) {
        $request->file('image')->move(public_path('images') . 'temp');
    }
    
    ....
}
```

Хорошо:

```
public function store(Request $request)
{
    $this->articleService->handleUploadedImage($request->file('image'));

    ....
}

class ArticleService
{
    public function handleUploadedImage($image)
    {
        if (!is_null($image)) {
            $image->move(public_path('images') . 'temp');
        }
    }
}
```

### **Не повторяйся (DRY)**

Этот принцип призывает вас переиспользовать код везде, где это возможно. Если вы следуете принципу SRP, вы уже избегаете повторений, но Laravel позволяет вам также переиспользовать представления, части Eloquent запросов и т.д.

Плохо:

```
public function getActive()
{
    return $this->where('verified', 1)->whereNotNull('deleted_at')->get();
}

public function getArticles()
{
    return $this->whereHas('user', function ($q) {
            $q->where('verified', 1)->whereNotNull('deleted_at');
        })->get();
}
```

Хорошо:

```
public function scopeActive($q)
{
    return $q->where('verified', 1)->whereNotNull('deleted_at');
}

public function getActive()
{
    return $this->active()->get();
}

public function getArticles()
{
    return $this->whereHas('user', function ($q) {
            $q->active();
        })->get();
}
```

### **Предпочитайте Eloquent конструктору запросов (query builder) и сырым запросам в БД. Предпочитайте работу с коллекциями работе с массивами**

Eloquent позволяет писать максимально читаемый код, а изменять функционал приложения несоизмеримо легче. У Eloquent также есть ряд удобных и мощных инструментов.

Плохо:

```
SELECT *
FROM `articles`
WHERE EXISTS (SELECT *
              FROM `users`
              WHERE `articles`.`user_id` = `users`.`id`
              AND EXISTS (SELECT *
                          FROM `profiles`
                          WHERE `profiles`.`user_id` = `users`.`id`) 
              AND `users`.`deleted_at` IS NULL)
AND `verified` = '1'
AND `active` = '1'
ORDER BY `created_at` DESC
```

Тоже плохо
```
$query = DB::table('selectionCars')->join('cars', 'selectionCars.car_id', '=', 'cars.id')
```
Хорошо:

```
Article::where('cars_id', 1)->get();
```

### **Используйте массовое заполнение (mass assignment)**

Плохо:

```
$article = new Article;
$article->title = $request->title;
$article->content = $request->content;
$article->verified = $request->verified;
// Привязать статью к категории.
$article->category_id = $category->id;
$article->save();
```

Хорошо:

```
$category->article()->create($request->validated());
```

### **Не выполняйте запросы в представлениях и используйте нетерпеливую загрузку (проблема N + 1)**

Плохо (будет выполнен 101 запрос в БД для 100 пользователей):

```
@foreach (User::all() as $user)
    {{ $user->profile->name }}
@endforeach
```

Хорошо (будет выполнено 2 запроса в БД для 100 пользователей):

```
$users = User::with('profile')->get();

...

@foreach ($users as $user)
    {{ $user->profile->name }}
@endforeach
```

### **Комментируйте код, предпочитайте читаемые имена методов комментариям**

Плохо:

```
if (count((array) $builder->getQuery()->joins) > 0)
```

Лучше:

```
// Determine if there are any joins.
if (count((array) $builder->getQuery()->joins) > 0)
```

Хорошо:

```
if ($this->hasJoins())
```

### **Выносите JS и CSS из шаблонов Blade и HTML из PHP кода**

Плохо:

```
let article = `{{ json_encode($article) }}`;
```

Лучше:

```
<input id="article" type="hidden" value="{{ json_encode($article) }}">

Или

<button class="js-fav-article" data-article="{{ json_encode($article) }}">{{ $article->name }}<button>
```

В Javascript файле:

```
let article = $('#article').val();
```

Еще лучше использовать специализированный пакет для передачи данных из бэкенда во фронтенд.

### **Конфиги, языковые файлы и константы вместо текста в коде**

Непосредственно в коде не должно быть никакого текста.

Плохо:

```
public function isNormal()
{
    return $article->type === 'normal';
}

return back()->with('message', 'Ваша статья была успешно добавлена');
```

Хорошо:

```
public function isNormal()
{
    return $article->type === Article::TYPE_NORMAL;
}

return back()->with('message', __('app.article_added'));
```

### **Используйте инструменты и практики принятые сообществом**

Laravel имеет встроенные инструменты для решения часто встречаемых задач. Предпочитайте пользоваться ими использованию сторонних пакетов и инструментов. Laravel разработчику, пришедшему в проект после вас, придется изучать и работать с новым для него инструментом, со всеми вытекающими последствиями. Получить помощь от сообщества будет также гораздо труднее. Не заставляйте клиента или работодателя платить за ваши велосипеды.

Задача | Стандартные инструмент | Нестандартные инструмент
------------ | ------------- | -------------
Авторизация | Политики | Entrust, Sentinel и др. пакеты, собственное решение
Работа с JS, CSS и пр. | Laravel Mix | Grunt, Gulp, сторонние пакеты
Среда разработки | Homestead | Docker
Разворачивание приложений | Laravel Forge | Deployer и многие другие
Тестирование | Phpunit, Mockery | Phpspec
e2e тестирование | Laravel Dusk | Codeception
Работа с БД | Eloquent | SQL, построитель запросов, Doctrine
Шаблоны | Blade | Twig
Работа с данными | Коллекции Laravel | Массивы
Валидация форм | Request классы | Сторонние пакеты, валидация в контроллере
Аутентификация | Встроенный функционал | Сторонние пакеты, собственное решение
Аутентификация API | Laravel Passport | Сторонние пакеты, использующие JWT, OAuth
Создание API | Встроенный функционал | Dingo API и другие пакеты
Работа со структурой БД | Миграции | Работа с БД напрямую
Локализация | Встроенный функционал | Сторонние пакеты
Обмен данными в реальном времени | Laravel Echo, Pusher | Пакеты и работа с веб сокетами напрямую
Генерация тестовых данных | Seeder классы, фабрики моделей, Faker | Ручное заполнение и пакеты
Планирование задач | Планировщик задач Laravel | Скрипты и сторонние пакеты
БД | MySQL, PostgreSQL, SQLite, SQL Server | MongoDb

### **Соблюдайте соглашения сообщества об именовании**

 Следуйте [стандартам PSR](http://www.php-fig.org/psr/psr-2/) при написании кода.
 
 Также, соблюдайте другие cоглашения об именовании:

Что | Правило | Принято | Не принято
------------ | ------------- | ------------- | -------------
Контроллер | ед. ч. | ArticleController | ~~ArticlesController~~
Маршруты | мн. ч. | articles/1 | ~~article/1~~
Имена маршрутов | snake_case | users.show_active | ~~users.show-active, show-active-users~~
Модель | ед. ч. | User | ~~Users~~
Отношения hasOne и belongsTo | ед. ч. | articleComment | ~~articleComments, article_comment~~
Все остальные отношения | мн. ч. | articleComments | ~~articleComment, article_comments~~
Таблица | мн. ч. | article_comments | ~~article_comment, articleComments~~
Pivot таблица | имена моделей в алфавитном порядке в ед. ч. | article_user | ~~user_article, articles_users~~
Столбец в таблице | snake_case без имени модели | meta_title | ~~MetaTitle; article_meta_title~~
Свойство модели | snake_case | $model->created_at | ~~$model->createdAt~~
Внешний ключ | имя модели ед. ч. и _id | article_id | ~~ArticleId, id_article, articles_id~~
Первичный ключ | - | id | ~~custom_id~~
Миграция | - | 2017_01_01_000000_create_articles_table | ~~2017_01_01_000000_articles~~
Метод | camelCase | getAll | ~~get_all~~
Метод в контроллере ресурсов | [таблица](https://laravel.com/docs/master/controllers#resource-controllers) | store | ~~saveArticle~~
Метод в тесте | camelCase | testGuestCannotSeeArticle | ~~test_guest_cannot_see_article~~
Переменные | camelCase | $articlesWithAuthor | ~~$articles_with_author~~
Коллекция | описательное, мн. ч. | $activeUsers = User::active()->get() | ~~$active, $data~~
Объект | описательное, ед. ч. | $activeUser = User::active()->first() | ~~$users, $obj~~
Индексы в конфиге и языковых файлах | snake_case | articles_enabled | ~~ArticlesEnabled; articles-enabled~~
Представление | kebab-case | show-filtered.blade.php | ~~showFiltered.blade.php, show_filtered.blade.php~~
Конфигурационный файл | snake_case | google_calendar.php | ~~googleCalendar.php, google-calendar.php~~
Контракт (интерфейс) | прилагательное или существительное | Authenticatable | ~~AuthenticationInterface, IAuthentication~~
Трейт | прилагательное | Notifiable | ~~NotificationTrait~~

### **Короткий и читаемый синтаксис там, где это возможно**

Плохо:

```
$request->session()->get('cart');
$request->input('name');
```

Хорошо:

```
session('cart');
$request->name;
```

Еще примеры:

Часто используемый синтаксис | Более короткий и читаемый синтаксис
------------ | -------------
`Session::get('cart')` | `session('cart')`
`$request->session()->get('cart')` | `session('cart')`
`Session::put('cart', $data)` | `session(['cart' => $data])`
`$request->input('name'), Request::get('name')` | `$request->name, request('name')`
`return Redirect::back()` | `return back()`
`is_null($object->relation) ? null : $object->relation->id` | `optional($object->relation)->id`
`return view('index')->with('title', $title)->with('client', $client)` | `return view('index', compact('title', 'client'))`
`$request->has('value') ? $request->value : 'default';` | `$request->get('value', 'default')`
`Carbon::now(), Carbon::today()` | `now(), today()`
`App::make('Class')` | `app('Class')`
`->where('column', '=', 1)` | `->where('column', 1)`
`->orderBy('created_at', 'desc')` | `->latest()`
`->orderBy('age', 'desc')` | `->latest('age')`
`->orderBy('created_at', 'asc')` | `->oldest()`
`->select('id', 'name')->get()` | `->get(['id', 'name'])`
`->first()->name` | `->value('name')`


### **Храните даты в стандартном формате. Используйте читатели и преобразователи для преобразования формата**

Плохо:

```
{{ Carbon::createFromFormat('Y-d-m H-i', $object->ordered_at)->toDateString() }}
{{ Carbon::createFromFormat('Y-d-m H-i', $object->ordered_at)->format('m-d') }}
```

Хорошо:

```
// Модель
protected $dates = ['ordered_at', 'created_at', 'updated_at'];
// Читатель (accessor)
public function getSomeDateAttribute($date)
{
    return $date->format('m-d');
}

// Шаблон
{{ $object->ordered_at->toDateString() }}
{{ $object->ordered_at->some_date }}
```

### **Именования роутов**
Плохо:
```
Route::get('/sendValidateSms','CheckoutController@sendValidateSms');
```

Хорошо:
```
Route::get('/sendValidateSms','CheckoutController@sendValidateSms')->name('sendValidateSms');
```
Таким образом мы сможем указать роут форме:
```
<form action={{route('sendValidateSms')}}>
</form>
```

### **Метод в контроллере ресурсов**
Хватит придумывать названия методово в контроллере для обычных задач. Они уже придуманы для нас. Только без и используй, не благодарите.

Метод | URL | Action | Имя роута
------------ | ------------- | ------------- | -------------
GET|/photos|index|photos.index
GET|/photos/create|create|photos.create
POST|/photos|store|photos.store
GET|/photos/{photo}|show|photos.show
GET|/photos/{photo}/edit|edit|photos.edit
PUT/PATCH|/photos/{photo}|update|photos.update
DELETE|/photos/{photo}|destroy|photos.destroy


### **Другие советы и практики**

Не размещайте логику в маршрутах.

Старайтесь не использовать "сырой" PHP в шаблонах Blade.
