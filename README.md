![GitHub language count](https://img.shields.io/github/languages/count/niece1/laravel-labels)
![GitHub top language](https://img.shields.io/github/languages/top/niece1/laravel-labels)
![GitHub repo size](https://img.shields.io/github/repo-size/niece1/laravel-labels)
![GitHub contributors](https://img.shields.io/github/contributors/niece1/laravel-labels)
![GitHub last commit](https://img.shields.io/github/last-commit/niece1/laravel-labels)
![GitHub](https://img.shields.io/github/license/niece1/laravel-labels)

## Intro

Laravel labels package. Add, remove label to any model you need. Created for personal use.

## Usage

Because this package isn't published on Packagist to install it into your Laravel project, follow the steps below:

In your composer.json file under require directive add:
```
"niece1/laravel-labels": "dev-main"
```

It should look like this:
```
"require": {
	"php": "7.3|8.0",
	**"niece1/laravel-labels": "dev-main",**
	"laravel/framework": "^8.65"
}
```
Also ypu need to add repositories directive:
```
repositories: [
    {
    	"type": "vcs",
    	"url": "https://github.com/niece1/laravel-labels.git"
    }
]
```
And run command:
```
composer update
```

The package will be installed into your project.

To perform database migrations run:
```
php artisan migrate
```

The package will automatically register its service provider.

After adding **Labelable** trait to the Model in which you want to have label (tag) functionality, you can use following methods:
```
- label();
- unlabel();
- relabel();
```

## License

This is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
