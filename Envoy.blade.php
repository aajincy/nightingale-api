@servers(['qa' => ['ubuntu@65.0.211.237'], 'localhost' => '127.0.0.1'])


@setup
    $now            = new DateTime();
    $environment    = isset($env) ? $env : "testing";
    $branch         = isset($branch) ? $branch : "master";
    $commitmessage  = isset($commitmessage) ? $commitmessage : "Update By Neetha on ".date("Y-m-d h:i sa"); 
@endsetup

@story('production')
    git-update
    git-pull
    composer-update
    migrate
    optimize
@endstory

@story('deploy')
    git-pull
    composer-update
    migrate
    optimize
@endstory

@task('git-update',['on' => ['localhost']])
    git add .
    git commit -am "{{$commitmessage}}"
    git push origin {{$branch}}
@endtask

@task('git-pull', ['on' => 'qa'])
    cd /var/www/html/backend
    git pull origin master
@endtask

@task('composer-update', ['on' => 'qa'])
    cd /var/www/html/backend
    composer install --optimize-autoloader --no-dev
    composer dump-autoload
@endtask

@task('migrate', ['on' => 'qa'])
    cd /var/www/html/backend
    php artisan migrate
@endtask

@task('optimize', ['on' => 'qa'])
    cd /var/www/html/backend
    php artisan optimize:clear
    php artisan cache:clear
    php artisan route:cache
    php artisan view:cache
@endtask