# Консоль для GIT 

ИД модуля: vasoft.git

## Возможности
Консоль для работы с git. Кроме команд связанных непосредственно с git допустимы команды: ls, diff, cd.
Существует история команд (для браузеров поддерживающих LocalStorage) и автоподсказки по командам.

## Ограничения
- Bitrix версии 24.0 или выше
- PHP версии 8.0 или выше
- Git установленный на сервере
- Функция PHP: proc_open

## Установка
- Установите модуль стандартным способом
- Настроить права доступа для нужной группы пользователей (W: запись)
- При необходимости настроить значение переменной окружения HOME в настройках модуля

## Применение
В разделе Настройки Панели управления появляется пункт меню "Консоль Git"

Консоль можно выводить на произвольной странице проекта подключив соответствующий модуль и добавив HTML блок для вывода. Пример:
```php
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
Bitrix\Main\UI\Extension::load(['ui.vue3', 'vasoft.git']);
?>
    <div id="git-console"></div>
    <script>
        const gitConsole = new BX.Vasoft.GitConsole('#git-console');
        gitConsole.start();
    </script>
<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
```
В данном примере консоль будет выведена не зависимо от прав текущего пользователя. Однако команды выполняются только если права у пользователя есть.

## Дополнительные возможности
Для навигации по истории команд - стрелки вверх и вниз.
Для применения подсказки по командам - табуляция.

## Дополнительная информация
- [Страница модуля](https://va-soft.ru/market/git/)
- За основу был взят скрипт Антона Медведева [Console](https://github.com/elfet/console)
- [Битрикс Маркетплейс](http://marketplace.1c-bitrix.ru/solutions/vasoft.git/)

