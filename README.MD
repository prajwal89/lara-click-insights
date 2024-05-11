# Lara Click Insights

Record impressions and clicks of any model with Lara Click Insights

## Installation

- Install the package via composer:

```bash
composer require prajwal89/lara-click-insights
```

- Publish frontend assets:

```bash
php artisan vendor:publish --tag=lara-click-insights-assets
```

- Publish Database migrations:

```bash
php artisan vendor:publish --tag=lara-click-insights-migrations
```

- Publish Database config (optional):

```bash
php artisan vendor:publish --tag=lara-click-insights-config
```

Alternatively, you can publish all of the above with the following command:

```bash
php artisan vendor:publish
```

When prompted, select Prajwal89\LaraClickInsights\LaraClickInsightsProvider.

In your HTML `<head>` tag, add the following directive and meta tag:

```html
<html>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    ...
    @LaraClickInsightsJs
  </head>
```

Do not forget to run `php artisan migrate`

## Usage

The Eloquent models you want to track impressions for should use the `Prajwal89\LaraClickInsights\Traits\ImpressionTrackable` trait.

```php
namespace App;

use Prajwal89\LaraClickInsights\Traits\ImpressionTrackable;
use Illuminate\Database\Eloquent\Model;

class YourEloquentModel extends Model
{
    use ImpressionTrackable;

    ...
}
```

The trait contains an abstract method `trackingAttribute()` that you must implement yourself on your frontend. this should look something like this

```html
<!-- cards that you want to track -->
@foreach($yourEloquentModels as $yourEloquentModel)
    <a href="/xyz" {!! $yourEloquentModel->trackingAttribute() !!}>
        <!-- card content -->
    </a>
@foreach
```

this will add data attribute for identifying the clickable link like `data-clickable="yourEloquentModels:23:default"`

do not forget to use `{!!  !!}`

<!-- ## Configuration

Below is the default configuration file contents for config/lara-click-insights.php:

```php
return [
    // Configuration options...
];
```

You can modify these options according to your needs. If you've published the configuration file, you can find it in your Laravel project's config directory. -->

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
