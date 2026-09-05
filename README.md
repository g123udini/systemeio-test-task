# Symfony skeleton

Базовый шаблон для разворачивания микросервисов. 
Включает базовые компонеты для работы с Request (ArgumentValueResolver) и обработки ошибок Response(Exception formatter)

Содержит конфигурации:

* Docker
* phpunit
* phpstan
* psalm
* php-cs-fixer
* Api Doc

### Разработка


[Старт нового приложения Skeleton](docs/SKELETON_NEW_APP.md)

[Приложение Skeleton в Docker](docs/DOCKER.md)

[Deploy приложения на Skeleton](docs/DEPLOY.md)

[Мануал Symfony](https://symfony.com/doc/current/index.html)



### Makefile

```shell
  build                           Собрать контейнеры
  pull                            Обновить образа
  up                              Поднять контейнеры
  stop                            Остановить контейнеры
  in-app                          Войти в app контейнер
  phpunit                         Прогнать тесты
  phpstan                         Статический анализ кода - phpstan
  psalm                           Статический анализ кода - psalm
  phpcscheck                      Проверка стиля кода
  check                           Проверить коммит перед отправкой
  phpcsfix                        Внести автоматические правки по стилю кода
  lint                            Запускает проверку контейнера
  doctrine                        Запускает проверку схемы doctrine
```

## Авторизация в приватных репозиториях

Для сборки проекта необходимо выписать персональный токен и указать его в .env.local

```yaml
GIT_TOKEN=токен
```
