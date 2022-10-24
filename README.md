<h1 align="center">Yii 2 Extended Project Template</h1>


### Install via composer 

```shell
composer create-project --prefer-dist deryabinsergey/yii2-app-extended:2
```

### Benefits

This Project Template based on Advanced Template with some modifications

- add [rmrevin / yii2-fontawesome](https://github.com/rmrevin/yii2-fontawesome) Asset Bundle with Font Awesome 5
- add [Bootstrap Dasboard Template](https://getbootstrap.com/docs/4.5/examples/dashboard/) to backend
- remove unique key from `user.username`. Authorization by email and password.
- add [RBAC](https://www.yiiframework.com/doc/guide/2.0/en/security-authorization#rbac) implementation for users. Use in backend.
- add User CRUD to backend
- make codeception tests valid, using [yii2-bootstrap5](https://github.com/yiisoft/yii2-bootstrap5)
- add some tests to backend / create user, acceptance tests for backend
- add selenium to `docker-compose.yml` and docker config at `frontend/tests/acceptance.suite.yml.example`
- change php to `yiisoftware/yii2-php:8.1-apache` in `Dockerfile`
- add config to `Dockerfile` for pretty URL
- add `urlManagerFrontend` to backend config for making links from backend to frontend

**Be careful** in `environments/dev/frontend/web/index-test.php` and `environments/dev/backend/web/index-test.php` removed code to start test on Docker: 
```
// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}
```

All information and documentation available at [Advanced Project Template](https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/README.md).

### Using RBAC

**Roles** and **permissions** defined in `common/config/params.php`
For adding new **permission**, **roles** and **assigned** permissions to roles - should create migration. For example see `console/migrations/m221002_102238_initialize_rbac_roles.php` 
For began i make this **permissions**

- **backend** - access to backend
- **userCreate** - add new users in backend
- **userRead** - view information about existed users 
- **userUpdate** - can update user information
- **userDelete** - can delete users (*NB: you can soft delete existed users, just set status deleted*)

and next **roles**

- **admin** has full permissions
- **manager** has full permissions without delete
- **viewer** has only read permissions

Default **Roles** and **Permissions** in tree view:

```
admin (role)
├─userDelete (permission)
│
└─manager (role)
  ├─userCreate (permission)
  ├─userUpdate (permission)
  │
  └─viever (role)
    │
    ├─backend (permission)
    └─userRead (permission)
```

### Acceptance tests

Complete next steps to enable acceptance tests:

- copy frontend  config file `frontend/tests/acceptance.suite.yml.example` to `frontend/tests/acceptance.suite.yml` and backend config file `backend/tests/acceptance.suite.yml.example` to `backend/tests/acceptance.suite.yml`
- uncomment sections `selenium-event-bus`, `selenium-sessions`, `selenium-session-queue`, `selenium-distributor`, `selenium-router`, `chrome`, `firefox` in `docker-compose.yml`
- build tests: `./vendor/bin/codecept build`

Run all tests: `./vendor/bin/codecept run`