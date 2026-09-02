<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
        @vite('resources/js/app.ts')
    </head>
    <body>
        <header>
            <div class="container">
                Header
                <hr>
            </div>
        </header>
        <div>
            <div class="container">
                <div class="row">
                    <div class="col col-12 col-md-3">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a href="{{ route('profile.index') }}">Profile</a>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('posts.index') }}">Posts</a>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('categories.index') }}">Categories</a>
                            </li>
                            <li class="list-group-item">
                                <a href="{{ route('post-trash.index') }}">PostsTrash</a>
                            </li>
                        </ul>
                    </div>
                    <main class="col col-12 col-md-9">
                        <x-notice />
                        <h1 class="h2">{{ $title }}</h1>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        <footer>
            <hr>
            <div class="container">
                footer
            </div>
        </footer>
    </body>
</html>
