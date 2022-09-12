# Пример компонента для 1С-Битрикс (Онлайн запись к специалисту)

Реализована форма записи для клиентов.

Стек технологий:

* PHP
* HTML5/CSS3 (Framework Bootstrap)
* JavaScript
* API Bitrix D7

Пользователю предоставляется выбор города, доступного специалиста в городе, свободные даты для записи, время записи. Далее происходит заполнение полей формы с данными о клиенте. После отправки данных формы, все данные проверяются на соответствие заданным праметрам, если возникает ошибка, об этом сообщается пользователю. При корректных входных данных скрипт проверяет на существование пользователя в базе, если нашёл, то обновляет данные на актуальные из формы, иначе создаётся новый пользователь. После записи данных в инфоблок, администратору сайта отправляется письмо о новой записи к специалисту, а самому клиенту письмо с соответствующим оформлением, с перечислением указанных им данных.

## Форма записи (выбор основных параметров)
![Форма записи](https://i.ibb.co/JWT334R/1.jpg)
