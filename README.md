<h1 align="center">Yii 2 Extended Project Template</h1>


### Install via composer 

```shell
composer create-project --prefer-dist deryabinsergey/yii2-app-extended
```

This Project Template based on Advanced Template with some modifications

- add [Bootstrap 5](https://github.com/yiisoft/yii2-bootstrap5)
- add [rmrevin / yii2-fontawesome](https://github.com/rmrevin/yii2-fontawesome) Asset Bundle with Font Awesome 5
- add [Bootstrap Dasboard Template](https://getbootstrap.com/docs/4.5/examples/dashboard/) to backend
- add `ActionColumn` implementation for Bootstrap 5 icons
- remove unique key from `user.username`. Authorization by email and password.
- add simple permissions - User property `admin`. Only Admin has access to backend. For most powerfull - use RBAC.
- add User CRUD to backend
- make codeception tests valid, using [yii2-bootstrap5](https://github.com/yiisoft/yii2-bootstrap5)
- add some tests to backend / create user
- add selenium to `docker-compose.yml` and docker config at `frontend/tests/acceptance.suite.yml.example`
- change php to `yiisoftware/yii2-php:8.0-apache` in `Dockerfile`
- add config to `Dockerfile` for pretty URL
- add `urlManagerFrontend` to backend config for making links from backend to frontend

**Be careful** in `environments/dev/frontend/web/index-test.php` removed code to start test on Docker: 
```
// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}
```

All information and documentation is at [Advanced Project Template](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md).